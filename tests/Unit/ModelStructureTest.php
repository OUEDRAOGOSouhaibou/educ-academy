<?php

namespace Tests\Unit;

use App\Enums\DocumentType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Document;
use App\Models\Month;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelStructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_mass_assignment_protection_blocks_role_and_status(): void
    {
        $user = new User([
            'name' => 'Jean Dupont',
            'phone' => '+226 70-12.34 56',
            'email' => 'jean@example.com',
            'password' => 'secret1234',
            'role' => 'admin',     // Doit être ignoré
            'status' => 'approved', // Doit être ignoré
        ]);

        // Vérification que le téléphone a été automatiquement normalisé
        $this->assertEquals('+22670123456', $user->phone);

        // Vérification que role et status n'ont pas été modifiés via le constructeur
        $this->assertEquals(UserRole::Student, $user->role);
        $this->assertEquals(UserStatus::Pending, $user->status);
        $this->assertNotEquals('admin', $user->getAttributes()['role']);
        $this->assertNotEquals('approved', $user->getAttributes()['status']);

        $user->save();

        // Par défaut en base de données : role student, status pending
        $user->refresh();
        $this->assertEquals(UserRole::Student, $user->role);
        $this->assertEquals(UserStatus::Pending, $user->status);
    }

    public function test_phone_normalization_utility(): void
    {
        $this->assertEquals('70123456', User::normalizePhone(' 70 12 34 56 '));
        $this->assertEquals('+22670123456', User::normalizePhone('+226 70-12.34-56'));
    }

    public function test_youtube_id_extraction_handles_multiple_url_formats(): void
    {
        $expectedId = 'dQw4w9WgXcQ';

        $urls = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://youtube.com/watch?v=dQw4w9WgXcQ&t=10s',
            'https://youtu.be/dQw4w9WgXcQ',
            'https://www.youtube.com/shorts/dQw4w9WgXcQ',
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'dQw4w9WgXcQ', // ID direct
        ];

        foreach ($urls as $url) {
            $this->assertEquals($expectedId, Video::extractYoutubeId($url), "Échec pour l'URL: {$url}");
        }

        $this->assertNull(Video::extractYoutubeId('https://example.com/not-a-video'));
        $this->assertNull(Video::extractYoutubeId(''));
    }

    public function test_school_year_and_month_ordering_by_school_year_position(): void
    {
        $schoolYear = SchoolYear::create([
            'label' => '2026-2027',
            'is_current' => true,
        ]);

        // Inséré dans le désordre calendaire
        $janvier = Month::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'Janvier',
            'calendar_number' => 1,
            'position' => 4,
            'is_published' => true,
        ]);

        $octobre = Month::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'Octobre',
            'calendar_number' => 10,
            'position' => 1,
            'is_published' => true,
        ]);

        $novembre = Month::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'Novembre',
            'calendar_number' => 11,
            'position' => 2,
            'is_published' => false,
        ]);

        // Tri par position : Octobre doit arriver avant Janvier !
        $orderedMonths = $schoolYear->months()->get();
        $this->assertEquals('Octobre', $orderedMonths->first()->name);
        $this->assertEquals('Janvier', $orderedMonths->last()->name);

        // Scope published
        $publishedMonths = Month::published()->ordered()->pluck('name')->toArray();
        $this->assertEquals(['Octobre', 'Janvier'], $publishedMonths);
    }

    public function test_document_formatted_size(): void
    {
        $doc1 = new Document(['file_size' => 1024 * 500]); // 500 Ko
        $this->assertEquals('500 Ko', $doc1->formatted_size);

        $doc2 = new Document(['file_size' => 1048576 * 2.5]); // 2.5 Mo
        $this->assertEquals('2,5 Mo', $doc2->formatted_size);
    }
}

