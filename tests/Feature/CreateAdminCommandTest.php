<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_admin_interactively(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Nom complet de l\'administrateur', 'Professeur Principal')
            ->expectsQuestion('Numéro de téléphone (identifiant de connexion)', '+226 70 88 99 00')
            ->expectsQuestion('Adresse e-mail (facultative, appuyez sur Entrée pour ignorer)', 'admin@educ-academy.local')
            ->expectsQuestion('Mot de passe (8 caractères minimum, saisie masquée)', 'secret1234')
            ->expectsQuestion('Confirmez le mot de passe', 'secret1234')
            ->expectsOutputToContain('Compte administrateur créé avec succès !')
            ->assertSuccessful();

        $admin = User::where('phone', '+22670889900')->first();
        $this->assertNotNull($admin);
        $this->assertEquals('Professeur Principal', $admin->name);
        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertEquals(UserStatus::Approved, $admin->status);
        $this->assertFalse($admin->must_change_password);
        $this->assertNotNull($admin->approved_at);
    }

    public function test_fails_if_password_mismatch(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Nom complet de l\'administrateur', 'Professeur')
            ->expectsQuestion('Numéro de téléphone (identifiant de connexion)', '70112233')
            ->expectsQuestion('Adresse e-mail (facultative, appuyez sur Entrée pour ignorer)', '')
            ->expectsQuestion('Mot de passe (8 caractères minimum, saisie masquée)', 'secret1234')
            ->expectsQuestion('Confirmez le mot de passe', 'differentPassword')
            ->expectsOutputToContain('Les deux mots de passe ne correspondent pas.')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['phone' => '70112233']);
    }
}
