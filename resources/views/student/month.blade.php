@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('student.dashboard') }}" class="text-decoration-none text-ea-primary fw-medium">
                    <i class="bi bi-arrow-left me-1"></i> Mes cours
                </a>
            </li>
            <li class="breadcrumb-item active fw-semibold" aria-current="page">{{ $month->name }}</li>
        </ol>
    </nav>

    <!-- En-tête du mois -->
    <div class="card card-ea border-0 bg-white p-4 shadow-sm mb-4 card-ea-top-primary">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge badge-ea-primary mb-1">
                    {{ $month->schoolYear->label ?? '' }} &bull; Position #{{ $month->position }}
                </span>
                <h2 class="h3 fw-bold mb-1 text-dark">Mathématiques &mdash; {{ $month->name }}</h2>
                <p class="text-muted small mb-0">
                    {{ $videos->count() }} vidéo(s) de cours et {{ $typeCounts['all'] }} document(s) PDF disponibles.
                </p>
            </div>
            <div>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-grid me-1"></i> Changer de mois
                </a>
            </div>
        </div>
    </div>

    <!-- Section 1 : Vidéos de cours -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-play-btn-fill text-danger me-2"></i> Vidéos de cours ({{ $videos->count() }})
            </h4>
        </div>

        <div class="row g-3">
            @forelse($videos as $video)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-ea border-0 bg-white shadow-sm h-100 overflow-hidden card-ea-hover">
                        <!-- Lecteur Façade YouTube (économiseur de data mobile : chargement au clic) -->
                        <div class="video-facade" data-youtube-id="{{ $video->youtube_id }}" data-title="{{ $video->title }}">
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" loading="lazy">
                            <div class="play-btn">
                                <i class="bi bi-play-fill"></i>
                            </div>
                        </div>

                        <div class="card-body p-3 d-flex flex-column">
                            <h6 class="fw-bold mb-1 text-dark">{{ $video->title }}</h6>
                            @if($video->description)
                                <p class="text-muted small mb-2 flex-grow-1">{{ $video->description }}</p>
                            @endif
                            <div class="mt-auto pt-2 text-muted small d-flex align-items-center">
                                <i class="bi bi-info-circle me-1 text-ea-primary"></i>
                                <span>Cliquez pour lancer la vidéo en streaming</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card card-ea border-0 bg-white p-4 text-center shadow-sm text-muted">
                        <i class="bi bi-camera-video-off fs-2 d-block mb-2 text-secondary"></i>
                        Aucune vidéo publiée pour ce mois actuellement.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2 : Documents PDF -->
    <div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-file-earmark-pdf-fill text-ea-primary me-2"></i> Fiches & Exercices PDF ({{ $documents->count() }})
            </h4>
        </div>

        <!-- Filtres de type de documents -->
        <div class="d-flex flex-wrap gap-1 mb-3">
            <a href="{{ route('student.months.show', $month) }}" class="btn btn-sm {{ empty($currentType) ? 'btn-primary' : 'btn-outline-secondary' }}">
                Tous ({{ $typeCounts['all'] }})
            </a>
            <a href="{{ route('student.months.show', [$month, 'type' => 'cours']) }}" class="btn btn-sm {{ $currentType === 'cours' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-journal-text me-1"></i> Cours ({{ $typeCounts['cours'] }})
            </a>
            <a href="{{ route('student.months.show', [$month, 'type' => 'serie']) }}" class="btn btn-sm {{ $currentType === 'serie' ? 'btn-info text-dark fw-bold' : 'btn-outline-secondary' }}">
                <i class="bi bi-list-check me-1"></i> Séries ({{ $typeCounts['serie'] }})
            </a>
            <a href="{{ route('student.months.show', [$month, 'type' => 'devoir']) }}" class="btn btn-sm {{ $currentType === 'devoir' ? 'btn-ea-accent' : 'btn-outline-secondary' }}">
                <i class="bi bi-pencil-square me-1"></i> Devoirs ({{ $typeCounts['devoir'] }})
            </a>
            <a href="{{ route('student.months.show', [$month, 'type' => 'corrige']) }}" class="btn btn-sm {{ $currentType === 'corrige' ? 'btn-success fw-bold' : 'btn-outline-secondary' }}">
                <i class="bi bi-check-circle me-1"></i> Corrigés ({{ $typeCounts['corrige'] }})
            </a>
        </div>

        <!-- Grille / Liste des documents PDF -->
        <div class="row g-3">
            @forelse($documents as $doc)
                <div class="col-12 col-md-6">
                    <div class="card card-ea card-ea-hover border-0 bg-white shadow-sm h-100 p-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-3 bg-ea-primary-light rounded-3 text-center flex-shrink-0" style="width: 55px; height: 55px;">
                                <i class="bi {{ $doc->type->icon() }} fs-4 text-ea-primary"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $doc->type->badgeClass() }}">
                                        {{ $doc->type->label() }}
                                    </span>
                                    <span class="text-secondary small fw-bold">
                                        <i class="bi bi-hdd me-1"></i>{{ $doc->formatted_size }}
                                    </span>
                                </div>

                                <h6 class="fw-bold mb-1 text-dark">{{ $doc->title }}</h6>
                                @if($doc->description)
                                    <p class="text-muted small mb-2">{{ $doc->description }}</p>
                                @endif

                                <div class="d-flex flex-wrap gap-2 mt-2 pt-2 border-top">
                                    <a href="{{ route('documents.view', $doc) }}" target="_blank" class="btn btn-outline-primary btn-sm flex-fill text-nowrap">
                                        <i class="bi bi-eye me-1"></i> Consulter
                                    </a>
                                    <a href="{{ route('documents.download', $doc) }}" class="btn btn-primary btn-sm flex-fill text-nowrap">
                                        <i class="bi bi-download me-1"></i> Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card card-ea border-0 bg-white p-4 text-center shadow-sm text-muted">
                        <i class="bi bi-file-earmark-x fs-2 d-block mb-2 text-secondary"></i>
                        Aucun document disponible pour cette catégorie.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
