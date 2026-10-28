<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Month;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\Video;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tableau de bord administrateur avec statistiques globales et accès rapides.
     */
    public function index(): View
    {
        $currentYear = SchoolYear::getCurrent();

        // 1. Statistiques des comptes élèves
        $totalStudents = User::where('role', UserRole::Student)->count();
        $approvedStudents = User::where('role', UserRole::Student)->where('status', UserStatus::Approved)->count();
        $pendingStudents = User::where('role', UserRole::Student)->where('status', UserStatus::Pending)->count();
        $blockedStudents = User::where('role', UserRole::Student)->where('status', UserStatus::Blocked)->count();

        // 2. Statistiques des contenus
        // Vidéos publiées : vidéos appartenant à un mois publié de l'année en cours
        $publishedVideosCount = 0;
        $totalDocumentsCount = 0;

        if ($currentYear) {
            $publishedMonthIds = $currentYear->months()->published()->pluck('id');
            $publishedVideosCount = Video::whereIn('month_id', $publishedMonthIds)->count();

            $allMonthIds = $currentYear->months()->pluck('id');
            $totalDocumentsCount = Document::whereIn('month_id', $allMonthIds)->count();
        }

        // 3. Derniers élèves inscrits
        $recentStudents = User::where('role', UserRole::Student)->latest()->take(5)->get();

        // 4. Mois de l'année en cours
        $months = $currentYear 
            ? $currentYear->months()->withCount(['videos', 'documents'])->get() 
            : collect();

        return view('admin.dashboard', [
            'schoolYear' => $currentYear,
            'stats' => [
                'total_students' => $totalStudents,
                'approved_students' => $approvedStudents,
                'pending_students' => $pendingStudents,
                'blocked_students' => $blockedStudents,
                'published_videos' => $publishedVideosCount,
                'total_documents' => $totalDocumentsCount,
            ],
            'recentStudents' => $recentStudents,
            'months' => $months,
        ]);
    }
}
