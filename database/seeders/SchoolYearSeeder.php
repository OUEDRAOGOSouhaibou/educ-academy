<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Month;
use App\Models\SchoolYear;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SchoolYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Année scolaire en cours
        $schoolYear = SchoolYear::firstOrCreate(
            ['label' => '2026-2027'],
            ['is_current' => true]
        );

        // 2. Les 9 mois scolaires d'Octobre à Juin
        $monthsData = [
            ['name' => 'Octobre',  'calendar_number' => 10, 'position' => 1, 'is_published' => true],
            ['name' => 'Novembre', 'calendar_number' => 11, 'position' => 2, 'is_published' => true],
            ['name' => 'Décembre', 'calendar_number' => 12, 'position' => 3, 'is_published' => true],
            ['name' => 'Janvier',  'calendar_number' => 1,  'position' => 4, 'is_published' => false],
            ['name' => 'Février',  'calendar_number' => 2,  'position' => 5, 'is_published' => false],
            ['name' => 'Mars',     'calendar_number' => 3,  'position' => 6, 'is_published' => false],
            ['name' => 'Avril',    'calendar_number' => 4,  'position' => 7, 'is_published' => false],
            ['name' => 'Mai',      'calendar_number' => 5,  'position' => 8, 'is_published' => false],
            ['name' => 'Juin',     'calendar_number' => 6,  'position' => 9, 'is_published' => false],
        ];

        $createdMonths = [];
        foreach ($monthsData as $data) {
            $month = Month::firstOrCreate(
                [
                    'school_year_id' => $schoolYear->id,
                    'calendar_number' => $data['calendar_number'],
                ],
                [
                    'name' => $data['name'],
                    'position' => $data['position'],
                    'is_published' => $data['is_published'],
                ]
            );
            $createdMonths[$data['name']] = $month;
        }

        // 3. Vidéos de démonstration pour le mois d'Octobre
        $octobre = $createdMonths['Octobre'];

        if ($octobre->videos()->count() === 0) {
            Video::create([
                'month_id' => $octobre->id,
                'title' => 'Continuité et limites : Rappels fondamentaux',
                'description' => 'Revue complète des notions de continuité, limites usuelles et formes indéterminées.',
                'youtube_id' => 'dQw4w9WgXcQ',
                'position' => 1,
            ]);

            Video::create([
                'month_id' => $octobre->id,
                'title' => 'Théorème des valeurs intermédiaires (TVI)',
                'description' => 'Démonstration et méthode pratique de rédaction pour le baccalauréat.',
                'youtube_id' => 'kJQP7kiw5Fk',
                'position' => 2,
            ]);
        }

        // 4. Documents PDF de démonstration
        if ($octobre->documents()->count() === 0) {
            // Création d'un contenu PDF minimal valide
            $dummyPdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000108 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF\n";

            $docs = [
                [
                    'title' => 'Cours : Continuité et limites',
                    'type' => DocumentType::Cours,
                    'original_name' => 'Cours-Continuite-Limites-Octobre.pdf',
                    'file_name' => 'demo_cours_octobre.pdf',
                    'position' => 1,
                ],
                [
                    'title' => 'Série d\'exercices n°1 : Continuité',
                    'type' => DocumentType::Serie,
                    'original_name' => 'Serie-1-Continuite-Octobre.pdf',
                    'file_name' => 'demo_serie1_octobre.pdf',
                    'position' => 2,
                ],
                [
                    'title' => 'Devoir surveillé n°1',
                    'type' => DocumentType::Devoir,
                    'original_name' => 'Devoir-Surveille-1.pdf',
                    'file_name' => 'demo_ds1_octobre.pdf',
                    'position' => 3,
                ],
                [
                    'title' => 'Corrigé officiel du devoir n°1',
                    'type' => DocumentType::Corrige,
                    'original_name' => 'Corrige-Devoir-1.pdf',
                    'file_name' => 'demo_corrige_ds1.pdf',
                    'position' => 4,
                ],
            ];

            foreach ($docs as $doc) {
                $path = 'documents/' . $doc['file_name'];
                Storage::disk('local')->put($path, $dummyPdfContent);

                Document::create([
                    'month_id' => $octobre->id,
                    'title' => $doc['title'],
                    'description' => 'Document pédagogique de Terminale Mathématiques.',
                    'type' => $doc['type'],
                    'file_path' => $path,
                    'original_name' => $doc['original_name'],
                    'file_size' => strlen($dummyPdfContent),
                    'position' => $doc['position'],
                ]);
            }
        }
    }
}
