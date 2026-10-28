<?php

namespace Tests\Feature;

use App\Enums\DocumentType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Document;
use App\Models\Month;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\Video;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_expected_structure(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Année scolaire
        $schoolYear = SchoolYear::where('is_current', true)->first();
        $this->assertNotNull($schoolYear);
        $this->assertEquals('2026-2027', $schoolYear->label);

        // 2. Mois : exactement 9 mois d'Octobre à Juin
        $months = $schoolYear->months()->get();
        $this->assertCount(9, $months);
        $this->assertEquals('Octobre', $months->first()->name);
        $this->assertEquals('Juin', $months->last()->name);

        // 3. Vidéos et documents sur Octobre
        $octobre = $months->first();
        $this->assertGreaterThanOrEqual(2, $octobre->videos()->count());
        $this->assertGreaterThanOrEqual(4, $octobre->documents()->count());

        // 4. Comptes élèves de démo
        $approvedStudent = User::where('phone', '70000001')->first();
        $this->assertNotNull($approvedStudent);
        $this->assertEquals(UserRole::Student, $approvedStudent->role);
        $this->assertEquals(UserStatus::Approved, $approvedStudent->status);

        $pendingStudent = User::where('phone', '70000002')->first();
        $this->assertNotNull($pendingStudent);
        $this->assertEquals(UserStatus::Pending, $pendingStudent->status);

        $blockedStudent = User::where('phone', '70000003')->first();
        $this->assertNotNull($blockedStudent);
        $this->assertEquals(UserStatus::Blocked, $blockedStudent->status);
    }
}

