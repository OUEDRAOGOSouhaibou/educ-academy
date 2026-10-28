@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.months.index') }}" class="text-decoration-none">Mois scolaires</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $month->name }}</li>
        </ol>
    </nav>

    <!-- En-tête du mois -->
    <div class="card card-ea border-0 bg-white p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h2 class="h3 fw-bold mb-0 text-dark">{{ $month->name }}</h2>
                    <span class="badge {{ $month->is_published ? 'bg-success' : 'bg-secondary' }}">
                        {{ $month->is_published ? 'Publié' : 'Masqué' }}
                    </span>
                    <span class="badge bg-light text-muted border">Position #{{ $month->position }}</span>
                </div>
                <p class="text-muted small mb-0">
                    Année {{ $month->schoolYear->label ?? '' }} &bull; {{ $month->videos->count() }} vidéo(s) &bull; {{ $month->documents->count() }} document(s) PDF
                </p>
            </div>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.months.toggle-publish', $month) }}">
                    @csrf
                    <button type="submit" class="btn {{ $month->is_published ? 'btn-outline-secondary' : 'btn-outline-success' }} btn-sm">
                        <i class="bi {{ $month->is_published ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i>
                        {{ $month->is_published ? 'Masquer le mois' : 'Publier le mois' }}
                    </button>
                </form>
                <button type="button" class="btn btn-danger btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                    <i class="bi bi-camera-video me-1"></i> Ajouter une vidéo
                </button>
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Téléverser un PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Section 1 : Vidéos de cours -->
    <div class="card card-ea border-0 bg-white shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-play-btn-fill text-danger me-2"></i> Vidéos de cours ({{ $month->videos->count() }})
            </h5>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                <i class="bi bi-plus-lg me-1"></i> Ajouter
            </button>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                @forelse($month->videos as $video)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 border shadow-none">
                            <!-- Façade vidéo lazy load -->
                            <div class="video-facade" data-youtube-id="{{ $video->youtube_id }}" data-title="{{ $video->title }}">
                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" loading="lazy">
                                <div class="play-btn">
                                    <i class="bi bi-play-fill"></i>
                                </div>
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="badge bg-light text-secondary border small">Ordre #{{ $video->position }}</span>
                                    <span class="text-muted small font-monospace">ID: {{ $video->youtube_id }}</span>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $video->title }}</h6>
                                @if($video->description)
                                    <p class="text-muted small mb-3 flex-grow-1">{{ $video->description }}</p>
                                @endif

                                <div class="mt-auto pt-2 border-top d-flex justify-content-end">
                                    <form method="POST" action="{{ route('admin.videos.destroy', $video) }}" class="d-inline" onsubmit="return confirm('Supprimer cette vidéo ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer la vidéo">
                                            <i class="bi bi-trash me-1"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-camera-video-off fs-2 d-block mb-2 text-secondary"></i>
                        Aucune vidéo pour ce mois. Cliquez sur « Ajouter une vidéo » pour coller un lien YouTube.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Section 2 : Documents PDF -->
    <div class="card card-ea border-0 bg-white shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-file-earmark-pdf-fill text-primary me-2"></i> Documents PDF ({{ $month->documents->count() }})
            </h5>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                <i class="bi bi-upload me-1"></i> Téléverser un PDF
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Ordre</th>
                        <th>Type</th>
                        <th>Titre du document</th>
                        <th>Taille</th>
                        <th>Fichier source</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($month->documents as $doc)
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-light text-secondary border">#{{ $doc->position }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $doc->type->badgeClass() }}">
                                    <i class="bi {{ $doc->type->icon() }} me-1"></i> {{ $doc->type->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $doc->title }}</div>
                                @if($doc->description)
                                    <div class="text-muted small">{{ $doc->description }}</div>
                                @endif
                            </td>
                            <td class="small fw-semibold text-secondary">
                                {{ $doc->formatted_size }}
                            </td>
                            <td class="small text-muted font-monospace text-truncate" style="max-width: 180px;">
                                {{ $doc->original_name }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('documents.view', $doc) }}" target="_blank" class="btn btn-outline-primary" title="Consulter dans le navigateur">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('documents.download', $doc) }}" class="btn btn-outline-success" title="Télécharger le fichier">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" class="d-inline" onsubmit="return confirm('Supprimer définitivement ce document et son fichier sur le disque ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer le document">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-x fs-2 d-block mb-2 text-secondary"></i>
                                Aucun document PDF téléversé pour ce mois.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modale d'ajout d'une vidéo YouTube -->
<div class="modal fade" id="addVideoModal" tabindex="-1" aria-labelledby="addVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.videos.store') }}">
            @csrf
            <input type="hidden" name="month_id" value="{{ $month->id }}">

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="addVideoModalLabel">
                        <i class="bi bi-camera-video me-2"></i> Ajouter une vidéo de cours
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="video_title" class="form-label fw-semibold">Titre de la vidéo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="video_title" name="title" placeholder="Ex: Continuité : Cours complet & Exemples" required>
                    </div>

                    <div class="mb-3">
                        <label for="youtube_url" class="form-label fw-semibold">Lien de la vidéo YouTube <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-youtube text-danger"></i></span>
                            <input type="text" class="form-control" id="youtube_url" name="youtube_url" placeholder="Ex: https://youtu.be/..." required>
                        </div>
                        <div class="form-text small">Formats acceptés : liens partagés YouTube, YouTube Shorts ou identifiant direct. Mode non répertorié recommandé.</div>
                    </div>

                    <div class="mb-3">
                        <label for="video_position" class="form-label fw-semibold">Ordre d'affichage</label>
                        <input type="number" class="form-control" id="video_position" name="position" value="{{ $month->videos->count() + 1 }}" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="video_description" class="form-label fw-semibold">Description ou conseils (facultatif)</label>
                        <textarea class="form-control" id="video_description" name="description" rows="2" placeholder="Notions clés abordées..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger fw-bold">Enregistrer la vidéo</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modale de téléversement d'un document PDF -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="month_id" value="{{ $month->id }}">

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addDocumentModalLabel">
                        <i class="bi bi-file-earmark-pdf me-2"></i> Téléverser un document PDF
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="doc_title" class="form-label fw-semibold">Titre du document <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="doc_title" name="title" placeholder="Ex: Cours - Continuité et limites" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="doc_type" class="form-label fw-semibold">Type de document <span class="text-danger">*</span></label>
                            <select class="form-select" id="doc_type" name="type" required>
                                <option value="cours">Cours</option>
                                <option value="serie">Série d'exercices</option>
                                <option value="devoir">Devoir</option>
                                <option value="corrige">Corrigé</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="doc_position" class="form-label fw-semibold">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="doc_position" name="position" value="{{ $month->documents->count() + 1 }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">Fichier PDF <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,application/pdf" required>
                        <div class="form-text small">Uniquement des fichiers PDF. Taille maximale autorisée : {{ config('app.pdf_max_size_mb', 20) }} Mo.</div>
                    </div>

                    <div class="mb-3">
                        <label for="doc_description" class="form-label fw-semibold">Description (facultative)</label>
                        <textarea class="form-control" id="doc_description" name="description" rows="2" placeholder="Précisions pour les élèves..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary fw-bold">Téléverser le PDF</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

