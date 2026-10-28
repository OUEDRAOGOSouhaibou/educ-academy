<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database in local development.
     */
    public function run(): void
    {
        // 1. Année scolaire et contenus de démonstration
        $this->call(SchoolYearSeeder::class);

        // 2. Comptes de démonstration pour le développement local
        // Élève autorisé (prêt à se connecter)
        $approvedStudent = User::firstOrNew(['phone' => '70000001']);
        $approvedStudent->name = 'Moussa Ouédraogo (Élève Autorisé)';
        $approvedStudent->password = Hash::make('password123');
        $approvedStudent->role = UserRole::Student;
        $approvedStudent->status = UserStatus::Approved;
        $approvedStudent->approved_at = now();
        $approvedStudent->must_change_password = false;
        $approvedStudent->save();

        // Élève en attente de validation
        $pendingStudent = User::firstOrNew(['phone' => '70000002']);
        $pendingStudent->name = 'Fatou Traoré (Élève En Attente)';
        $pendingStudent->password = Hash::make('password123');
        $pendingStudent->role = UserRole::Student;
        $pendingStudent->status = UserStatus::Pending;
        $pendingStudent->must_change_password = false;
        $pendingStudent->save();

        // Élève bloqué
        $blockedStudent = User::firstOrNew(['phone' => '70000003']);
        $blockedStudent->name = 'Ali Kaboré (Élève Bloqué)';
        $blockedStudent->password = Hash::make('password123');
        $blockedStudent->role = UserRole::Student;
        $blockedStudent->status = UserStatus::Blocked;
        $blockedStudent->must_change_password = false;
        $blockedStudent->save();
    }
}
