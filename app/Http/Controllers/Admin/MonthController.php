<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MonthRequest;
use App\Models\Month;
use App\Models\SchoolYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MonthController extends Controller
{
    /**
     * Affiche la liste des mois de l'année scolaire en cours.
     */
    public function index(): View
    {
        $schoolYear = SchoolYear::getCurrent() ?? SchoolYear::latest()->first();
        
        $months = $schoolYear 
            ? $schoolYear->months()->withCount(['videos', 'documents'])->get() 
            : collect();

        return view('admin.months.index', [
            'schoolYear' => $schoolYear,
            'months' => $months,
        ]);
    }

    /**
     * Affiche le contenu d'un mois spécifique (vidéos et documents).
     */
    public function show(Month $month): View
    {
        $month->load([
            'schoolYear',
            'videos' => fn ($q) => $q->orderBy('position'),
            'documents' => fn ($q) => $q->orderBy('position'),
        ]);

        return view('admin.months.show', [
            'month' => $month,
        ]);
    }

    /**
     * Enregistre un nouveau mois.
     */
    public function store(MonthRequest $request): RedirectResponse
    {
        $month = Month::create($request->validated());

        return redirect()->route('admin.months.show', $month)->with(
            'success',
            "Le mois de {$month->name} a été ajouté avec succès."
        );
    }

    /**
     * Met à jour les informations d'un mois.
     */
    public function update(MonthRequest $request, Month $month): RedirectResponse
    {
        $month->update($request->validated());

        return back()->with('success', "Les informations du mois de {$month->name} ont été mises à jour.");
    }

    /**
     * Bascule le statut de publication d'un mois (publié / masqué).
     */
    public function togglePublish(Month $month): RedirectResponse
    {
        $month->is_published = ! $month->is_published;
        $month->save();

        $action = $month->is_published ? 'publié (accessible aux élèves)' : 'masqué (inaccessible aux élèves)';

        return back()->with('success', "Le mois de {$month->name} est maintenant {$action}.");
    }

    /**
     * Supprime un mois et tous ses contenus (avec nettoyage physique des PDF).
     */
    public function destroy(Month $month): RedirectResponse
    {
        $monthName = $month->name;
        $month->delete();

        return redirect()->route('admin.months.index')->with(
            'info',
            "Le mois de {$monthName} et ses contenus associés ont été supprimés."
        );
    }
}

