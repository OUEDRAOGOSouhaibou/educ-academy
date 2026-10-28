<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_defaults_to_student_pending(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'Koffi Paul',
            'phone' => '+226 70 55 44 33',
            'email' => 'koffi@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertRedirect(route('student.pending'));

        $this->assertAuthenticated();

        $user = User::where('phone', '+22670554433')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Koffi Paul', $user->name);
        $this->assertEquals(UserRole::Student, $user->role);
        $this->assertEquals(UserStatus::Pending, $user->status);
        $this->assertFalse($user->must_change_password);
    }

    public function test_registration_prevents_privilege_escalation_via_mass_assignment(): void
    {
        $response = $this->post('/inscription', [
            'name' => 'Hacker Tentative',
            'phone' => '71-22-33-44',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('student.pending'));

        $user = User::where('phone', '71223344')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::Student, $user->role);
        $this->assertEquals(UserStatus::Pending, $user->status);
    }

    public function test_student_can_login_with_phone_containing_spaces_and_hyphens(): void
    {
        $student = User::create([
            'name' => 'Awa Sanou',
            'phone' => '+22670112233',
            'password' => 'password123',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Approved;
        $student->save();

        $response = $this->post('/connexion', [
            'phone' => ' +226 70-11.22 33 ',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_pending_student_is_redirected_to_pending_notice_page(): void
    {
        $student = User::create([
            'name' => 'Élève En Attente',
            'phone' => '70998877',
            'password' => 'password123',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Pending;
        $student->save();

        $this->actingAs($student);

        // Tente d'accéder au tableau de bord élève
        $response = $this->get('/espace-eleve');
        $response->assertRedirect(route('student.pending'));

        // L'accès à la page d'attente elle-même doit réussir
        $responseNotice = $this->get(route('student.pending'));
        $responseNotice->assertOk();
        $responseNotice->assertSee('Inscription en attente');
    }

    public function test_blocked_student_is_immediately_logged_out_on_next_request(): void
    {
        $student = User::create([
            'name' => 'Élève Va Être Bloqué',
            'phone' => '70443322',
            'password' => 'password123',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Approved;
        $student->save();

        $this->actingAs($student);

        // Session active valide
        $this->get('/espace-eleve')->assertOk();

        // L'administrateur bloque l'élève en base
        $student->status = UserStatus::Blocked;
        $student->save();

        // Requête suivante : doit être immédiatement déconnecté et redirigé vers login avec message
        $response = $this->get('/espace-eleve');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_temporary_password_forces_change_before_access(): void
    {
        $student = User::create([
            'name' => 'Élève Reset',
            'phone' => '70123459',
            'password' => 'tempPass123',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Approved;
        $student->must_change_password = true;
        $student->save();

        $this->actingAs($student);

        // Redirigé vers changement de mot de passe
        $response = $this->get('/espace-eleve');
        $response->assertRedirect(route('password.change'));

        // Formulaire de changement de mot de passe
        $updateResponse = $this->post(route('password.change.update'), [
            'password' => 'nouveauMotDePasse1',
            'password_confirmation' => 'nouveauMotDePasse1',
        ]);

        $updateResponse->assertRedirect(route('student.dashboard'));

        $student->refresh();
        $this->assertFalse($student->must_change_password);

        // Accès maintenant autorisé
        $this->get('/espace-eleve')->assertOk();
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::create([
            'name' => 'Élève Ordinaire',
            'phone' => '70999999',
            'password' => 'password123',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Approved;
        $student->save();

        $this->actingAs($student);

        $response = $this->get('/admin/eleves');
        $response->assertForbidden();
    }
}

