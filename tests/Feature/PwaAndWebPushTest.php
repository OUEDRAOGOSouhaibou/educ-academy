<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\WebPushService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PwaAndWebPushTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Élève PWA',
            'phone' => '+22670112233',
            'password' => 'secret1234',
        ]);
        $this->student->role = UserRole::Student;
        $this->student->status = UserStatus::Pending;
        $this->student->save();

        $this->admin = User::create([
            'name' => 'Professeur Admin',
            'phone' => '+22670998877',
            'password' => 'secret1234',
        ]);
        $this->admin->role = UserRole::Admin;
        $this->admin->status = UserStatus::Approved;
        $this->admin->save();
    }

    public function test_manifest_json_is_accessible_and_valid(): void
    {
        $response = $this->get('/manifest.json');
        $response->assertOk();
        $response->assertJsonFragment([
            'short_name' => "Educ'Academy",
            'display' => 'standalone',
            'theme_color' => '#095b86',
        ]);
    }

    public function test_service_worker_is_accessible(): void
    {
        $response = $this->get('/sw.js');
        $response->assertOk();
        $response->assertSee('self.addEventListener(\'push\'', false);
        $response->assertSee('self.addEventListener(\'notificationclick\'', false);
    }

    public function test_offline_page_is_accessible(): void
    {
        $response = $this->get('/offline.html');
        $response->assertOk();
        $response->assertSee('Vous êtes hors connexion');
        $response->assertSee("Educ'Academy", false);
    }

    public function test_vapid_public_key_endpoint_returns_valid_key(): void
    {
        $response = $this->get('/push/public-key');
        $response->assertOk();
        $response->assertJsonStructure(['publicKey']);
        $this->assertNotEmpty($response->json('publicKey'));
    }

    public function test_user_can_subscribe_to_push_notifications(): void
    {
        $response = $this->actingAs($this->student)->postJson('/push/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-token',
            'keys' => [
                'p256dh' => 'BLc4xRzKInY8S5wN4f3q0w1x2y3z4a5b6c7d8e9f',
                'auth' => 'a1b2c3d4e5f6',
            ],
            'contentEncoding' => 'aes128gcm',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $this->student->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-token',
        ]);
    }

    public function test_user_can_unsubscribe_from_push_notifications(): void
    {
        PushSubscription::create([
            'user_id' => $this->student->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/endpoint-to-delete',
            'public_key' => 'dummy-public-key',
            'auth_token' => 'dummy-auth-token',
        ]);

        $response = $this->postJson('/push/unsubscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/endpoint-to-delete',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/endpoint-to-delete',
        ]);
    }

    public function test_approving_student_sends_activation_push_notification(): void
    {
        $mockService = Mockery::mock(WebPushService::class);
        $mockService->shouldReceive('sendToUser')
            ->once()
            ->with(
                Mockery::on(fn($user) => $user->id === $this->student->id),
                Mockery::pattern('/Compte activé/'),
                Mockery::any(),
                Mockery::any()
            )
            ->andReturn(1);

        $this->app->instance(WebPushService::class, $mockService);

        $response = $this->actingAs($this->admin)->post(route('admin.students.approve', $this->student));
        $response->assertRedirect();

        $this->student->refresh();
        $this->assertTrue($this->student->isApproved());
    }

    public function test_admin_can_broadcast_push_notification(): void
    {
        $mockService = Mockery::mock(WebPushService::class);
        $mockService->shouldReceive('sendToAllApprovedStudents')
            ->once()
            ->with('Rappel de cours', 'Pensez à regarder la vidéo de Mathématiques', Mockery::any())
            ->andReturn(5);

        $this->app->instance(WebPushService::class, $mockService);

        $response = $this->actingAs($this->admin)->post(route('admin.students.broadcast-push'), [
            'title' => 'Rappel de cours',
            'message' => 'Pensez à regarder la vidéo de Mathématiques',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
