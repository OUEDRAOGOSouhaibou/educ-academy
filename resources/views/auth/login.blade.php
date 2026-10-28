@extends('layouts.auth')

@section('content')
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-1 text-dark">Espace de Connexion</h4>
        <p class="text-muted small mb-0">Consultez vos cours en vidéo et vos séries d'exercices</p>
    </div>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Numéro de téléphone -->
        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold small text-secondary">Numéro de téléphone <span class="text-danger">*</span></label>
            <div class="input-group auth-input-group">
                <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                <input type="tel" 
                       class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}" 
                       placeholder="Ex: 70 12 34 56" 
                       required 
                       autofocus 
                       autocomplete="tel">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text text-muted" style="font-size: 0.8rem;">Saisissez votre numéro avec ou sans espaces.</div>
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
                       placeholder="Votre mot de passe" 
                       required 
                       autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Se souvenir de moi -->
        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label small text-secondary" for="remember">Se souvenir de moi sur cet appareil</label>
        </div>

        <!-- Bouton de connexion aux couleurs du logo -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-auth-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
            </button>
        </div>

        <!-- Note WhatsApp Mot de passe oublié -->
        <div class="auth-info-box my-3 text-center">
            <i class="bi bi-whatsapp text-success me-1 fs-6"></i>
            <span>Mot de passe oublié ? <strong>Contactez votre professeur</strong> par WhatsApp pour obtenir un code temporaire.</span>
        </div>

        <hr class="my-4 text-muted opacity-25">

        <!-- Inscription nouvel élève avec bouton d'accent orange -->
        <div class="text-center">
            <p class="text-muted small mb-2">Vous êtes un nouvel élève de Terminale ?</p>
            <a href="{{ route('register') }}" class="btn btn-auth-register w-100">
                <i class="bi bi-person-plus-fill me-1"></i> Créer mon compte élève
            </a>
        </div>
    </form>
@endsection
