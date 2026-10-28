@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-ea border-0 bg-white p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary mb-2">Terminale &bull; Mathématiques</span>
                <h2 class="h3 fw-bold mb-1 text-dark">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="text-muted small mb-0">Consultez vos cours en vidéo et téléchargez vos fiches d'exercices organisés par mois.</p>
            </div>
            <div class="text-md-end">
                <span class="badge bg-success px-3 py-2 fs-6">
                    <i class="bi bi-patch-check-fill me-1"></i> Compte autorisé
                </span>
            </div>
        </div>
    </div>

    <!-- Message transitoire pour la Phase 4 -->
    <div class="alert alert-info d-flex align-items-center shadow-sm">
        <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
        <div>
            <strong>Authentification réussie !</strong> Le module de consultation des vidéos et de téléchargement des PDF par mois sera déployé dans la Phase 5.
        </div>
    </div>
</div>
@endsection
