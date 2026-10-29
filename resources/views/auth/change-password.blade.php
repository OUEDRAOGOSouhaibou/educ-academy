@extends('layouts.auth')

@section('content')
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-ea-primary-light rounded-circle mb-2" style="width: 56px; height: 56px; border: 2px solid rgba(9, 91, 134, 0.2);">
            <i class="bi bi-key-fill fs-3 text-ea-primary"></i>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Nouveau mot de passe</h4>
        <p class="text-muted small">Vous devez définir un nouveau mot de passe pour sécuriser votre compte.</p>
    </div>

    <form method="POST" action="{{ route('password.change.update') }}" novalidate>
        @csrf

        <!-- Nouveau mot de passe -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-lock text-ea-primary"></i></span>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Au moins 8 caractères" 
                       required 
                       autofocus 
                       autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Confirmation du mot de passe -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">Confirmer le mot de passe <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill text-ea-primary"></i></span>
                <input type="password" 
                       class="form-control" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       placeholder="Retapez le nouveau mot de passe" 
                       required 
                       autocomplete="new-password">
            </div>
        </div>

        <!-- Bouton d'enregistrement -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-auth-primary btn-lg shadow-sm">
                <i class="bi bi-check-circle me-2"></i> Mettre à jour et continuer
            </button>
        </div>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">
                    <i class="bi bi-box-arrow-right me-1"></i> Se déconnecter
                </button>
            </form>
        </div>
    </form>
@endsection
