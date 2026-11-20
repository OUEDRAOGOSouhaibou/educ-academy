<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushService
{
    protected ?WebPush $webPush = null;

    public function __construct()
    {
        // Compatibilité Windows OpenSSL si applicable
        $possibleConfigs = [
            'C:/xampp/apache/conf/openssl.cnf',
            'C:/xampp/php/extras/ssl/openssl.cnf',
        ];
        foreach ($possibleConfigs as $path) {
            if (file_exists($path) && !getenv('OPENSSL_CONF')) {
                putenv("OPENSSL_CONF=$path");
                break;
            }
        }

        $vapid = config('services.webpush.vapid');

        if (!empty($vapid['public_key']) && !empty($vapid['private_key'])) {
            try {
                $this->webPush = new WebPush([
                    'VAPID' => [
                        'subject' => $vapid['subject'] ?? 'mailto:admin@educ-academy.alwaysdata.net',
                        'publicKey' => $vapid['public_key'],
                        'privateKey' => $vapid['private_key'],
                    ],
                ]);
                $this->webPush->setAutomaticPadding(false);
            } catch (Throwable $e) {
                Log::error("Erreur d'initialisation WebPush : " . $e->getMessage());
                $this->webPush = null;
            }
        }
    }

    public function isConfigured(): bool
    {
        return $this->webPush !== null;
    }

    /**
     * Envoie une notification push vers une souscription spécifique.
     */
    public function sendToSubscription(PushSubscription $sub, array $payload): bool
    {
        if (!$this->webPush) {
            return false;
        }

        try {
            $subscription = Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
                'contentEncoding' => $sub->content_encoding ?: 'aes128gcm',
            ]);

            $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $report = $this->webPush->sendOneNotification($subscription, $jsonPayload);

            if ($report->isSuccess()) {
                return true;
            }

            // Si le jeton est expiré ou le terminal désinscrit (404/410), nettoyer la base
            if ($report->isSubscriptionExpired()) {
                $sub->delete();
                Log::info("Souscription push expirée supprimée (ID: {$sub->id})");
            } else {
                Log::warning("Échec envoi push (ID: {$sub->id}) : " . $report->getReason());
            }

            return false;
        } catch (Throwable $e) {
            Log::error("Exception WebPush : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie une notification à un utilisateur donné.
     */
    public function sendToUser(User $user, string $title, string $body, ?string $url = null, ?string $icon = null): int
    {
        $payload = [
            'title' => $title,
            'body' => $body,
            'icon' => $icon ?: url('/images/icons/icon-192x192.png'),
            'badge' => url('/images/icons/icon-192x192.png'),
            'data' => [
                'url' => $url ?: url('/espace-eleve'),
            ],
        ];

        $sentCount = 0;
        foreach ($user->pushSubscriptions as $sub) {
            if ($this->sendToSubscription($sub, $payload)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Envoie une notification à un groupe d'utilisateurs.
     */
    public function sendToUsers(iterable $users, string $title, string $body, ?string $url = null, ?string $icon = null): int
    {
        $totalSent = 0;
        foreach ($users as $user) {
            $totalSent += $this->sendToUser($user, $title, $body, $url, $icon);
        }
        return $totalSent;
    }

    /**
     * Diffuse une notification à tous les élèves autorisés.
     */
    public function sendToAllApprovedStudents(string $title, string $body, ?string $url = null, ?string $icon = null): int
    {
        $students = User::where('role', UserRole::Student)
            ->where('status', UserStatus::Approved)
            ->with('pushSubscriptions')
            ->get();

        return $this->sendToUsers($students, $title, $body, $url, $icon);
    }
}
