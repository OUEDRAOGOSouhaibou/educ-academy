@extends('layouts.app')

@section('content')
<div class="container">

    <!-- En-tête -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <span class="badge bg-primary mb-1">Année scolaire : {{ $schoolYear->label ?? 'Non définie' }}</span>
            <h2 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-calendar3 text-primary me-2"></i> Organisation des Mois & Contenus
            </h2>
            <p class="text-muted small mb-0">Les vidéos et PDF sont organisés par mois d'apprentissage (Octobre &rarr; Juin).</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createMonthModal">
                <i class="bi bi-plus-circle me-1"></i> Ajouter un mois
            </button>
        </div>
    </div>

    <!-- Grille des mois -->
    <div class="row g-3">
        @forelse($months as $month)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-ea h-100 border-0 bg-white shadow-sm {{ $month->is_published ? 'border-start border-success border-4' : 'border-start border-secondary border-4' }}">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-secondary border small mb-1">Position #{{ $month->position }}</span>
                                <h4 class="fw-bold mb-0 text-dark">{{ $month->name }}</h4>
                            </div>
                            <span class="badge {{ $month->is_published ? 'bg-success' : 'bg-secondary' }}">
                                {{ $month->is_published ? 'Publié' : 'Masqué' }}
                            </span>
                        </div>

                        <p class="text-muted small mb-3">Mois calendaire : n°{{ $month->calendar_number }}</p>

                        <!-- Statistiques de contenu du mois -->
                        <div class="d-flex gap-2 my-auto py-2">
                            <div class="flex-fill p-2 bg-light rounded text-center">
                                <i class="bi bi-camera-video-fill text-danger me-1"></i>
                                <span class="fw-bold">{{ $month->videos_count }}</span>
                                <span class="text-muted small d-block">Vidéos</span>
                            </div>
                            <div class="flex-fill p-2 bg-light rounded text-center">
                                <i class="bi bi-file-earmark-pdf-fill text-primary me-1"></i>
                                <span class="fw-bold">{{ $month->documents_count }}</span>
                                <span class="text-muted small d-block">PDF</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <form method="POST" action="{{ route('admin.months.toggle-publish', $month) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $month->is_published ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="{{ $month->is_published ? 'Masquer pour les élèves' : 'Publier pour les élèves' }}">
                                    <i class="bi {{ $month->is_published ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i>
                                    {{ $month->is_published ? 'Masquer' : 'Publier' }}
                                </button>
                            </form>

                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.months.show', $month) }}" class="btn btn-primary fw-semibold">
                                    <i class="bi bi-folder2-open me-1"></i> Contenus
                                </a>
                                <form method="POST" action="{{ route('admin.months.destroy', $month) }}" class="d-inline" onsubmit="return confirm('Attention : supprimer ce mois supprimera également toutes ses vidéos et tous ses fichiers PDF. Confirmer ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer le mois">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-ea border-0 bg-white p-5 text-center shadow-sm">
                    <i class="bi bi-calendar-x fs-1 text-muted mb-2"></i>
                    <h5 class="fw-bold text-dark">Aucun mois configuré</h5>
                    <p class="text-muted small mb-3">Ajoutez les mois de l'année scolaire pour commencer à publier des cours.</p>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMonthModal">
                            <i class="bi bi-plus-circle me-1"></i> Ajouter le premier mois
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

<!-- Modale de création d'un mois -->
<div class="modal fade" id="createMonthModal" tabindex="-1" aria-labelledby="createMonthModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.months.store') }}">
            @csrf
            @if($schoolYear)
                <input type="hidden" name="school_year_id" value="{{ $schoolYear->id }}">
            @endif
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createMonthModalLabel">
                        <i class="bi bi-calendar-plus me-2"></i> Ajouter un mois scolaire
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nom du mois <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ex: Octobre, Novembre..." required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="calendar_number" class="form-label fw-semibold">Mois calendaire (1-12) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="calendar_number" name="calendar_number" min="1" max="12" placeholder="10 pour Octobre" required>
                        </div>
                        <div class="col-6">
                            <label for="position" class="form-label fw-semibold">Ordre dans l'année <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="position" name="position" min="1" max="50" value="{{ ($months->max('position') ?? 0) + 1 }}" required>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" checked>
                        <label class="form-check-label fw-medium" for="is_published">Publier immédiatement (visible par les élèves autorisés)</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-bold">Enregistrer le mois</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

