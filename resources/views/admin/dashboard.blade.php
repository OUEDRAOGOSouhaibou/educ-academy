@extends('layouts.app')

@section('content')
<div class="container">

    <!-- En-tête -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <span class="badge bg-danger mb-1">Espace Administration</span>
            <h2 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-speedometer2 text-primary me-2"></i> Tableau de bord Professeur
            </h2>
            <p class="text-muted small mb-0">Vue d'ensemble de la plateforme Educ'Academy &bull; Année {{ $schoolYear->label ?? 'non définie' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.months.index') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-calendar-plus me-1"></i> Gérer les Mois
            </a>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-dark btn-sm shadow-sm">
                <i class="bi bi-people me-1"></i> Gérer les Élèves
            </a>
        </div>
    </div>

    <!-- Alertes inscriptions en attente si > 0 -->
    @if($stats['pending_students'] > 0)
        <div class="alert alert-warning border-warning d-flex justify-content-between align-items-center shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3"></i>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $stats['pending_students'] }} élève(s) en attente de validation</h6>
                    <small class="text-secondary">Validez leurs inscriptions pour leur permettre d'accéder aux cours.</small>
                </div>
            </div>
            <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm fw-bold text-dark px-3">
                Valider les élèves
            </a>
        </div>
    @endif

    <!-- Cartes de métriques clés -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="card card-ea border-0 bg-white p-3 shadow-sm text-center">
                <div class="text-muted small mb-1">Total élèves</div>
                <div class="h3 fw-bold text-dark mb-0">{{ $stats['total_students'] }}</div>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card card-ea border-0 {{ $stats['pending_students'] > 0 ? 'bg-warning bg-opacity-10 border border-warning' : 'bg-white' }} p-3 shadow-sm text-center">
                    <div class="text-warning fw-semibold small mb-1">En attente</div>
                    <div class="h3 fw-bold text-dark mb-0">{{ $stats['pending_students'] }}</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card card-ea border-0 bg-white p-3 shadow-sm text-center">
                <div class="text-success fw-semibold small mb-1">Autorisés</div>
                <div class="h3 fw-bold text-success mb-0">{{ $stats['approved_students'] }}</div>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card card-ea border-0 bg-white p-3 shadow-sm text-center">
                <div class="text-danger fw-semibold small mb-1">Bloqués</div>
                <div class="h3 fw-bold text-danger mb-0">{{ $stats['blocked_students'] }}</div>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card card-ea border-0 bg-white p-3 shadow-sm text-center">
                <div class="text-muted small mb-1">Vidéos en ligne</div>
                <div class="h3 fw-bold text-primary mb-0">{{ $stats['published_videos'] }}</div>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card card-ea border-0 bg-white p-3 shadow-sm text-center">
                <div class="text-muted small mb-1">Documents PDF</div>
                <div class="h3 fw-bold text-primary mb-0">{{ $stats['total_documents'] }}</div>
            </div>
        </div>
    </div>

    <!-- Deux sections côte à côte : Derniers élèves & Mois de l'année -->
    <div class="row g-4">
        <!-- Derniers élèves inscrits -->
        <div class="col-12 col-lg-6">
            <div class="card card-ea border-0 bg-white shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i> Dernières inscriptions
                    </h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-primary btn-sm">Voir tous</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Élève</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $student)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $student->name }}</div>
                                        <small class="text-muted">{{ $student->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="small font-monospace">{{ $student->phone }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $student->status->badgeClass() }} small">
                                            {{ $student->status->label() }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($student->isPending())
                                            <form method="POST" action="{{ route('admin.students.approve', $student) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm py-0 px-2" title="Autoriser">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aucun élève inscrit pour l'instant.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mois et contenus scolaires -->
        <div class="col-12 col-lg-6">
            <div class="card card-ea border-0 bg-white shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-calendar-check text-primary me-2"></i> Mois scolaires ({{ $months->count() }})
                    </h5>
                    <a href="{{ route('admin.months.index') }}" class="btn btn-outline-primary btn-sm">Gérer</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Mois</th>
                                <th>Contenus</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Lien</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($months as $month)
                                <tr>
                                    <td class="ps-3 fw-semibold">
                                        <span class="badge bg-light text-secondary border me-1">#{{ $month->position }}</span>
                                        {{ $month->name }}
                                    </td>
                                    <td class="small text-muted">
                                        <span class="me-2"><i class="bi bi-camera-video text-danger me-1"></i>{{ $month->videos_count }}</span>
                                        <span><i class="bi bi-file-earmark-pdf text-primary me-1"></i>{{ $month->documents_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $month->is_published ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $month->is_published ? 'Publié' : 'Masqué' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.months.show', $month) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" title="Gérer ce mois">
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aucun mois configuré.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

