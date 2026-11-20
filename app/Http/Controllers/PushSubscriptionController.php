<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Fournit la clé publique VAPID nécessaire au navigateur.
     */
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.webpush.vapid.public_key'),
        ]);
    }

    /**
     * Enregistre ou met à jour la souscription push d'un utilisateur.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'url'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string'],
        ]);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint' => $validated['endpoint']],
            [
                'user_id' => Auth::id(),
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['contentEncoding'] ?? 'aes128gcm',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Souscription aux notifications enregistrée avec succès.',
            'id' => $subscription->id,
        ]);
    }

    /**
     * Supprime la souscription push d'un terminal.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'url'],
        ]);

        PushSubscription::where('endpoint', $validated['endpoint'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Souscription aux notifications supprimée.',
        ]);
    }

    /**
     * Envoie une notification push de test sur le terminal de l'utilisateur connecté.
     */
    public function testNotification(WebPushService $webPushService): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $sent = $webPushService->sendToUser(
            $user,
            "✨ Test Educ'Academy",
            "Votre téléphone est parfaitement configuré pour recevoir les notifications d'Educ'Academy !",
            url('/espace-eleve')
        );

        return response()->json([
            'success' => $sent > 0,
            'sent_count' => $sent,
            'message' => $sent > 0 ? 'Notification envoyée avec succès !' : 'Aucun terminal actif trouvé.',
        ]);
    }
}
