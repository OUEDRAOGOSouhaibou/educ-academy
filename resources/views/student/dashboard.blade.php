@extends('layouts.app')

@section('content')
<div class="container">

    <!-- En-tête de bienvenue de l'élève -->
    <div class="card card-ea border-0 bg-white p-4 shadow-sm mb-4 card-ea-top-accent">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge badge-ea-primary mb-2">
                    <i class="bi bi-mortarboard-fill me-1 text-ea-accent"></i> Terminale &bull; Mathématiques
                </span>
                <h2 class="h3 fw-bold mb-1 text-dark">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="text-muted small mb-0">
                    Année scolaire <strong>{{ $schoolYear->label ?? 'en cours' }}</strong> &bull; Tous vos cours et exercices classés par mois d'apprentissage.
                </p>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i> Compte autorisé
                </span>
            </div>
        </div>
    </div>

    <!-- Titre de la section des mois -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-calendar3 text-ea-primary me-2"></i> Mois au programme
        </h4>
        <span class="text-muted small">
            {{ $months->count() }} mois disponible(s)
        </span>
    </div>

    <!-- Grille des mois publiés -->
    <div class="row g-3">
        @forelse($months as $month)
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('student.months.show', $month) }}" class="text-decoration-none">
                    <div class="card card-ea card-ea-hover border-0 bg-white shadow-sm h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-light text-secondary border small">Position #{{ $month->position }}</span>
                                <span class="badge badge-ea-primary">
                                    <i class="bi bi-folder2 me-1"></i> Disponible
                                </span>
                            </div>

                            <h4 class="fw-bold text-dark mb-1">{{ $month->name }}</h4>
                            <p class="text-muted small mb-3">Programme de Mathématiques &bull; Terminale</p>

                            <!-- Indicateurs de contenus -->
                            <div class="d-flex gap-2 my-auto py-2">
                                <div class="flex-fill p-2 bg-light rounded text-center">
                                    <i class="bi bi-camera-video-fill text-danger me-1"></i>
                                    <span class="fw-bold text-dark">{{ $month->videos_count }}</span>
                                    <span class="text-muted small d-block">Vidéo(s)</span>
                                </div>
                                <div class="flex-fill p-2 bg-light rounded text-center">
                                    <i class="bi bi-file-earmark-pdf-fill text-ea-primary me-1"></i>
                                    <span class="fw-bold text-dark">{{ $month->documents_count }}</span>
                                    <span class="text-muted small d-block">PDF</span>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top text-ea-primary fw-bold small d-flex justify-content-between align-items-center">
                                <span>Ouvrir les cours du mois</span>
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-ea border-0 bg-white p-5 text-center shadow-sm">
                    <i class="bi bi-hourglass-split fs-1 text-ea-accent mb-2"></i>
                    <h5 class="fw-bold text-dark">Aucun mois publié pour le moment</h5>
                    <p class="text-muted small mb-0">
                        Votre professeur n'a pas encore publié les premiers cours de l'année. Revenez très bientôt !
                    </p>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
