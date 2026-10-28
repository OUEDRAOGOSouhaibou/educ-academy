@extends('layouts.auth')

@section('content')
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-1">Inscription Élève</h4>
        <p class="text-muted small">Créez votre compte pour suivre les cours de Terminale</p>
    </div>

    <!-- Note d'information sur la validation préalable -->
    <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-4 small" role="alert">
        <i class="bi bi-shield-check fs-5 me-2 flex-shrink-0"></i>
        <div>
            Après inscription, votre compte sera activé par le professeur pour vous donner accès aux contenus.
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <!-- Nom complet -->
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nom complet de l'élève <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                <input type="text" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       placeholder="Ex: Moussa Ouédraogo" 
                       required 
                       autofocus 
                       autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Numéro de téléphone -->
        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">Numéro de téléphone WhatsApp <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-telephone text-muted"></i></span>
                <input type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       placeholder="Ex: 70 12 34 56 ou +226..." 
                       required 
                       autocomplete="tel">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text small">Ce numéro vous servira d'identifiant de connexion unique.</div>
        </div>

        <!-- Adresse e-mail (facultative) -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Adresse e-mail <span class="text-muted small fw-normal">(facultative)</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       placeholder="nom@exemple.com" 
                       autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Mot de passe -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Mot de passe <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Au moins 8 caractères" 
                       required 
                       autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Confirmation du mot de passe -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">Confirmer le mot de passe <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-muted"></i></span>
                <input type="password" 
                       class="form-control" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       placeholder="Retapez le mot de passe" 
                       required 
                       autocomplete="new-password">
            </div>
        </div>

        <!-- Bouton d'inscription -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark shadow-sm">
                <i class="bi bi-check-lg me-2"></i> Valider mon inscription
            </button>
        </div>

        <hr class="my-4">

        <!-- Lien de connexion -->
        <div class="text-center">
            <p class="text-muted small mb-0">Vous avez déjà un compte ?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm mt-2 w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
            </a>
        </div>
    </form>
@endsection
