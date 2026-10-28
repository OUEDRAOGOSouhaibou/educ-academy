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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $approvedStudent;
    private SchoolYear $schoolYear;
    private Month $publishedMonth;
    private Month $unpublishedMonth;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::create([
            'name' => 'Professeur Admin',
            'phone' => '+22670000000',
            'password' => 'secret1234',
        ]);
        $this->admin->role = UserRole::Admin;
        $this->admin->status = UserStatus::Approved;
        $this->admin->save();

        $this->approvedStudent = User::create([
            'name' => 'Élève Autorisé',
            'phone' => '+22670111111',
            'password' => 'secret1234',
        ]);
        $this->approvedStudent->role = UserRole::Student;
        $this->approvedStudent->status = UserStatus::Approved;
        $this->approvedStudent->save();

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
            'name' => 'Janvier',
            'calendar_number' => 1,
            'position' => 4,
            'is_published' => false,
        ]);
    }

    public function test_admin_can_create_month_video_and_upload_pdf(): void
    {
        // 1. Créer un mois
        $monthResponse = $this->actingAs($this->admin)->post(route('admin.months.store'), [
            'school_year_id' => $this->schoolYear->id,
            'name' => 'Novembre',
            'calendar_number' => 11,
            'position' => 2,
            'is_published' => 1,
        ]);
        $monthResponse->assertSessionHas('success');
        $this->assertDatabaseHas('months', ['name' => 'Novembre', 'position' => 2]);
        $novembre = Month::where('name', 'Novembre')->first();

        // 2. Ajouter une vidéo
        $videoResponse = $this->actingAs($this->admin)->post(route('admin.videos.store'), [
            'month_id' => $novembre->id,
            'title' => 'Dérivation & Étude de fonctions',
            'description' => 'Méthodes de calcul de dérivées.',
            'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'position' => 1,
        ]);
        $videoResponse->assertSessionHas('success');
        $this->assertDatabaseHas('videos', [
            'month_id' => $novembre->id,
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);

        // 3. Téléverser un PDF
        $fakePdf = UploadedFile::fake()->create('fiche_cours.pdf', 1024, 'application/pdf');

        $docResponse = $this->actingAs($this->admin)->post(route('admin.documents.store'), [
            'month_id' => $novembre->id,
            'title' => 'Fiche synthétique Dérivation',
            'type' => DocumentType::Cours->value,
            'position' => 1,
            'file' => $fakePdf,
        ]);

        $docResponse->assertSessionHas('success');
        $document = Document::where('title', 'Fiche synthétique Dérivation')->first();
        $this->assertNotNull($document);

        // Vérifier que le fichier est stocké sur le disque privé
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_approved_student_can_download_and_view_pdf_from_published_month(): void
    {
        $fakePdf = UploadedFile::fake()->create('serie1.pdf', 500, 'application/pdf');
        $path = $fakePdf->store('documents', 'local');

        $document = Document::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Série 1 Octobre',
            'type' => DocumentType::Serie,
            'file_path' => $path,
            'original_name' => 'serie1.pdf',
            'file_size' => 500 * 1024,
            'position' => 1,
        ]);

        // L'élève approuvé peut télécharger
        $downloadResponse = $this->actingAs($this->approvedStudent)
            ->get(route('documents.download', $document));
        $downloadResponse->assertOk();

        // L'élève approuvé peut consulter en ligne
        $viewResponse = $this->actingAs($this->approvedStudent)
            ->get(route('documents.view', $document));
        $viewResponse->assertOk();
    }

    public function test_approved_student_cannot_access_pdf_from_unpublished_month(): void
    {
        $fakePdf = UploadedFile::fake()->create('devoir_secret.pdf', 300, 'application/pdf');
        $path = $fakePdf->store('documents', 'local');

        $secretDoc = Document::create([
            'month_id' => $this->unpublishedMonth->id, // Janvier est masqué
            'title' => 'Devoir surveillé secret',
            'type' => DocumentType::Devoir,
            'file_path' => $path,
            'original_name' => 'devoir.pdf',
            'file_size' => 300 * 1024,
            'position' => 1,
        ]);

        // L'élève tente d'y accéder par URL directe : 403 Forbidden
        $response = $this->actingAs($this->approvedStudent)
            ->get(route('documents.download', $secretDoc));
        $response->assertForbidden();

        $viewResponse = $this->actingAs($this->approvedStudent)
            ->get(route('documents.view', $secretDoc));
        $viewResponse->assertForbidden();

        // L'administrateur, lui, a accès même si le mois est masqué
        $adminResponse = $this->actingAs($this->admin)
            ->get(route('documents.download', $secretDoc));
        $adminResponse->assertOk();
    }

    public function test_guest_cannot_download_pdf(): void
    {
        $fakePdf = UploadedFile::fake()->create('cours.pdf', 200, 'application/pdf');
        $path = $fakePdf->store('documents', 'local');

        $document = Document::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Cours public test',
            'type' => DocumentType::Cours,
            'file_path' => $path,
            'original_name' => 'cours.pdf',
            'file_size' => 200 * 1024,
            'position' => 1,
        ]);

        $response = $this->get(route('documents.download', $document));
        $response->assertRedirect(route('login'));
    }

    public function test_replacing_or_deleting_pdf_removes_physical_file_from_disk(): void
    {
        // 1. Téléverser le premier fichier PDF A
        $fileA = UploadedFile::fake()->create('fichierA.pdf', 400, 'application/pdf');
        $pathA = $fileA->store('documents', 'local');

        $document = Document::create([
            'month_id' => $this->publishedMonth->id,
            'title' => 'Document évolutif',
            'type' => DocumentType::Cours,
            'file_path' => $pathA,
            'original_name' => 'fichierA.pdf',
            'file_size' => 400 * 1024,
            'position' => 1,
        ]);

        Storage::disk('local')->assertExists($pathA);

        // 2. Mettre à jour avec un fichier B
        $fileB = UploadedFile::fake()->create('fichierB.pdf', 600, 'application/pdf');

        $this->actingAs($this->admin)->put(route('admin.documents.update', $document), [
            'month_id' => $this->publishedMonth->id,
            'title' => 'Document évolutif v2',
            'type' => DocumentType::Cours->value,
            'file' => $fileB,
        ]);

        $document->refresh();

        // Fichier A doit être supprimé du disque
        Storage::disk('local')->assertMissing($pathA);
        // Fichier B doit exister sur le disque
        Storage::disk('local')->assertExists($document->file_path);

        $pathB = $document->file_path;

        // 3. Supprimer le document
        $this->actingAs($this->admin)->delete(route('admin.documents.destroy', $document));

        // Fichier B doit être définitivement supprimé du disque
        Storage::disk('local')->assertMissing($pathB);
    }

    public function test_non_pdf_file_or_exceeding_max_size_is_rejected(): void
    {
        // Fichier Word au lieu de PDF
        $fakeDocx = UploadedFile::fake()->create('document.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $responseDocx = $this->actingAs($this->admin)->post(route('admin.documents.store'), [
            'month_id' => $this->publishedMonth->id,
            'title' => 'Faux document',
            'type' => DocumentType::Cours->value,
            'file' => $fakeDocx,
        ]);

        $responseDocx->assertSessionHasErrors('file');

        // Fichier dépassant 20 Mo (ex: 20500 Ko > 20480 Ko)
        $oversizedPdf = UploadedFile::fake()->create('huge.pdf', 20500, 'application/pdf');

        $responseOversized = $this->actingAs($this->admin)->post(route('admin.documents.store'), [
            'month_id' => $this->publishedMonth->id,
            'title' => 'Fichier trop lourd',
            'type' => DocumentType::Cours->value,
            'file' => $oversizedPdf,
        ]);

        $responseOversized->assertSessionHasErrors('file');
    }

    public function test_invalid_youtube_url_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.videos.store'), [
            'month_id' => $this->publishedMonth->id,
            'title' => 'Vidéo invalide',
            'youtube_url' => 'https://example.com/not-youtube',
        ]);

        $response->assertSessionHasErrors('youtube_url');
    }
}

