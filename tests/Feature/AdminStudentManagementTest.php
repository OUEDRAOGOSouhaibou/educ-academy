<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Professeur Admin',
            'phone' => '+22670000000',
            'password' => 'adminPassword123',
        ]);
        $this->admin->role = UserRole::Admin;
        $this->admin->status = UserStatus::Approved;
        $this->admin->save();
    }

    public function test_admin_can_view_student_list(): void
    {
        User::create([
            'name' => 'Élève Un',
            'phone' => '70111111',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.students.index'));
        $response->assertOk();
        $response->assertSee('Élève Un');
        $response->assertSee('70111111');
    }

    public function test_admin_can_approve_pending_student(): void
    {
        $student = User::create([
            'name' => 'Élève À Valider',
            'phone' => '70222222',
            'password' => 'secret123',
        ]);

        $this->assertEquals(UserStatus::Pending, $student->status);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.students.approve', $student));

        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertEquals(UserStatus::Approved, $student->status);
        $this->assertNotNull($student->approved_at);
    }

    public function test_admin_can_block_and_unblock_student(): void
    {
        $student = User::create([
            'name' => 'Élève Suspect',
            'phone' => '70333333',
            'password' => 'secret123',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Approved;
        $student->save();

        // 1. Bloquer
        $this->actingAs($this->admin)
            ->post(route('admin.students.block', $student));

        $student->refresh();
        $this->assertEquals(UserStatus::Blocked, $student->status);

        // 2. Débloquer
        $this->actingAs($this->admin)
            ->post(route('admin.students.unblock', $student));

        $student->refresh();
        $this->assertEquals(UserStatus::Approved, $student->status);
    }

    public function test_admin_can_generate_temporary_password_for_whatsapp(): void
    {
        $student = User::create([
            'name' => 'Élève Oubli MDP',
            'phone' => '70444444',
            'password' => 'ancienMotDePasse',
        ]);
        $student->role = UserRole::Student;
        $student->status = UserStatus::Approved;
        $student->save();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.students.reset-password', $student));

        $response->assertSessionHas('temp_password');
        $tempPassword = session('temp_password');
        $this->assertNotEmpty($tempPassword);

        $student->refresh();
        $this->assertTrue($student->must_change_password);

        // Déconnexion préalable de l'administrateur
        $this->post('/deconnexion');

        // L'élève doit pouvoir se connecter avec ce mot de passe temporaire
        $this->post('/connexion', [
            'phone' => '70444444',
            'password' => $tempPassword,
        ])->assertRedirect(route('password.change'));
    }

    public function test_admin_can_delete_student(): void
    {
        $student = User::create([
            'name' => 'Élève À Supprimer',
            'phone' => '70555555',
            'password' => 'secret123',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.students.destroy', $student));

        $this->assertDatabaseMissing('users', ['phone' => '70555555']);
    }
}
