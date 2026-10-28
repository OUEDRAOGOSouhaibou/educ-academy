@extends('layouts.auth')

@section('content')
    <div class="text-center py-2">
        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-25 text-warning rounded-circle mb-3" style="width: 70px; height: 70px;">
            <i class="bi bi-hourglass-split fs-1 text-warning"></i>
        </div>

        <h4 class="fw-bold mb-2">Inscription en attente</h4>
        
        <p class="text-muted mb-3">
            Bonjour <strong>{{ $student->name }}</strong>, votre compte a bien été créé avec le numéro :
        </p>

        <div class="p-2 mb-4 bg-light rounded border text-center fw-bold fs-5 text-primary">
            <i class="bi bi-telephone me-2"></i> {{ $student->phone }}
        </div>

        <div class="alert alert-info text-start small mb-4">
            <i class="bi bi-info-circle-fill me-1"></i>
            Votre inscription doit être validée par votre professeur de mathématiques avant que vous ne puissiez accéder aux vidéos et aux documents PDF.
        </div>

        <p class="text-secondary small mb-4">
            Dès que le professeur aura validé votre demande, vous aurez un accès direct et permanent à tous les cours de l'année scolaire.
        </p>

        <div class="d-grid gap-2">
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-clockwise me-1"></i> Actualiser mon statut
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link text-muted text-decoration-none btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Se déconnecter
                </button>
            </form>
        </div>
    </div>
@endsection
