@extends('layouts.auth')

@section('content')
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-1 text-dark">Inscription Élève</h4>
        <p class="text-muted small mb-0">Créez votre compte pour suivre les cours de Terminale</p>
    </div>

    <!-- Note d'information sur la validation préalable -->
    <div class="alert alert-primary bg-opacity-10 border-primary border-opacity-25 d-flex align-items-center py-2 px-3 mb-4 small rounded-3" role="alert">
        <i class="bi bi-shield-check fs-5 me-2 text-ea-primary flex-shrink-0"></i>
        <div class="text-secondary">
            Après inscription, votre compte sera activé par le professeur pour vous donner accès aux vidéos et PDF.
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <!-- Nom complet -->
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold small text-secondary">Nom complet de l'élève <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
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
            <label for="phone" class="form-label fw-semibold small text-secondary">Numéro de téléphone WhatsApp <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
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
            <div class="form-text text-muted" style="font-size: 0.8rem;">Ce numéro vous servira d'identifiant unique de connexion.</div>
        </div>

        <!-- Adresse e-mail (facultative) -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small text-secondary">Adresse e-mail <span class="text-muted fw-normal">(facultative)</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
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
            <label for="password" class="form-label fw-semibold small text-secondary">Mot de passe <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
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
            <label for="password_confirmation" class="form-label fw-semibold small text-secondary">Confirmer le mot de passe <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                <input type="password" 
                       class="form-control" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       placeholder="Retapez le mot de passe" 
                       required 
                       autocomplete="new-password">
            </div>
        </div>

        <!-- Bouton d'inscription (accent orange élégant) -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-ea-accent btn-lg fw-bold shadow-sm py-2">
                <i class="bi bi-check2-circle me-2"></i> Valider mon inscription
            </button>
        </div>

        <hr class="my-4 text-muted opacity-25">

        <!-- Lien vers la connexion -->
        <div class="text-center">
            <p class="text-muted small mb-2">Vous avez déjà un compte ?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1 text-ea-primary"></i> Se connecter
            </a>
        </div>
    </form>
@endsection
