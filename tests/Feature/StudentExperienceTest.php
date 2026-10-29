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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentExperienceTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $admin;
    private SchoolYear $schoolYear;
    private Month $publishedMonth;
    private Month $unpublishedMonth;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->schoolYear = SchoolYear::create([
            'label' => '2026-2027',
            'is_current' => true,
        ]);

        $this->publishedMonth = Month::create([
            'school_year_id' => $this->schoolYear->id,
            'name' => 'Octobre',
            'calendar_number' => 10,
            'position' => 1,
            'is_published' => true,
        ]);

        $this->unpublishedMonth = Month::create([
            'school_year_id' => $this->schoolYear->id,
            'name' => 'Février',
            'calendar_number' => 2,
            'position' => 5,
            'is_published' => false,
        ]);

        $this->student = User::create([
            'name' => 'Élève Terminale C',
            'phone' => '+22670123456',
            'password' => 'secret1234',
        ]);
        $this->student->role = UserRole::Student;
        $this->student->status = UserStatus::Approved;
        $this->student->save();

        $this->admin = User::create([
            'name' => 'Professeur Principal',
            'phone' => '+22670987654',
            'password' => 'secret1234',
        ]);
        $this->admin->role = UserRole::Admin;
        $this->admin->status = UserStatus::Approved;
        $this->admin->save();
    }

    public function test_landing_page_loads_with_educ_academy_branding(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee("Educ'");
        $response->assertSee('Academy');
        $response->assertSee('Terminale');
        $response->assertSee('Mathématiques');
        $response->assertSee('Se connecter');
        $response->assertSee('inscrire');
        $response->assertSee('Construis ton avenir et Prépare-toi à le Vivre');
    }

    public function test_student_dashboard_displays_published_months(): void
    {
        $response = $this->actingAs($this->student)->get(route('student.dashboard'));
        $response->assertOk();
        $response->assertSee('Octobre');
        $response->assertDontSee('Février'); // Mois masqué non visible
    }

    public function test_student_can_view_published_month_content(): void
    {
        // Vidéo de test
        Video::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Vidéo Limites et Continuité',
            'youtube_id' => 'dQw4w9WgXcQ',
            'position' => 1,
        ]);

        // Document de test
        Document::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Fiche synthétique Cours',
            'type' => DocumentType::Cours,
            'file_path' => 'documents/dummy.pdf',
            'original_name' => 'fiche.pdf',
            'file_size' => 1024 * 350,
            'position' => 1,
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.months.show', $this->publishedMonth));

        $response->assertOk();
        $response->assertSee('Vidéo Limites et Continuité');
        $response->assertSee('Fiche synthétique Cours');
        $response->assertSee('350 Ko');
    }

    public function test_student_can_filter_documents_by_type_on_month_page(): void
    {
        Document::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Cours Théorie',
            'type' => DocumentType::Cours,
            'file_path' => 'documents/c.pdf',
            'original_name' => 'c.pdf',
            'file_size' => 1024,
            'position' => 1,
        ]);

        Document::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Série Exercices 1',
            'type' => DocumentType::Serie,
            'file_path' => 'documents/s.pdf',
            'original_name' => 's.pdf',
            'file_size' => 1024,
            'position' => 2,
        ]);

        // Filtre 'serie' : ne doit afficher que la série
        $response = $this->actingAs($this->student)
            ->get(route('student.months.show', [$this->publishedMonth, 'type' => 'serie']));

        $response->assertOk();
        $response->assertSee('Série Exercices 1');
        $response->assertDontSee('Cours Théorie');
    }

    public function test_student_cannot_access_unpublished_month_page(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.months.show', $this->unpublishedMonth));

        $response->assertForbidden();
    }

    public function test_admin_dashboard_displays_stats_and_overview(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Tableau de bord Professeur');
        $response->assertSee('Total élèves');
        $response->assertSee('Vidéos en ligne');
    }
}
