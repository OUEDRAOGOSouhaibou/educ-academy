<?php

namespace App\Http\Controllers\Student;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Month;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tableau de bord élève : liste des mois publiés de l'année en cours.
     */
    public function index(): View
    {
        $schoolYear = SchoolYear::getCurrent();

        $publishedMonths = $schoolYear
            ? $schoolYear->months()
                ->published()
                ->withCount(['videos', 'documents'])
                ->get()
            : collect();

        return view('student.dashboard', [
            'schoolYear' => $schoolYear,
            'months' => $publishedMonths,
        ]);
    }

    /**
     * Consultation des contenus (vidéos et documents PDF) d'un mois spécifique.
     */
    public function showMonth(Request $request, Month $month): View
    {
        // Règle stricte : Le mois doit être publié et appartenir à l'année scolaire en cours
        if (! $month->is_published || ! $month->schoolYear || ! $month->schoolYear->is_current) {
            abort(403, 'Ce mois de cours n\'est pas encore accessible.');
        }

        $typeFilter = $request->query('type');
        
        $documentsQuery = $month->documents()->ordered();
        if ($typeFilter && in_array($typeFilter, array_column(DocumentType::cases(), 'value'))) {
            $documentsQuery->where('type', $typeFilter);
        }

        $documents = $documentsQuery->get();
        $videos = $month->videos()->ordered()->get();

        // Compteurs par type de document pour les boutons de filtre
        $typeCounts = [
            'all' => $month->documents()->count(),
            DocumentType::Cours->value => $month->documents()->ofType(DocumentType::Cours)->count(),
            DocumentType::Serie->value => $month->documents()->ofType(DocumentType::Serie)->count(),
            DocumentType::Devoir->value => $month->documents()->ofType(DocumentType::Devoir)->count(),
            DocumentType::Corrige->value => $month->documents()->ofType(DocumentType::Corrige)->count(),
        ];

        return view('student.month', [
            'month' => $month,
            'videos' => $videos,
            'documents' => $documents,
            'currentType' => $typeFilter,
            'typeCounts' => $typeCounts,
        ]);
    }
}
