@extends('layouts.auth')

@section('content')
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-1">Connexion</h4>
        <p class="text-muted small">Accédez à vos cours et exercices de Mathématiques</p>
    </div>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Numéro de téléphone -->
        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">Numéro de téléphone <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-telephone text-muted"></i></span>
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
            <div class="form-text small">Saisissez votre numéro avec ou sans espaces.</div>
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
                       placeholder="Votre mot de passe" 
                       required 
                       autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Se souvenir de moi -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label small" for="remember">Se souvenir de moi sur cet appareil</label>
        </div>

        <!-- Bouton de soumission -->
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
            </button>
        </div>

        <!-- Lien Mot de passe oublié (spécifique V1 WhatsApp) -->
        <div class="text-center my-3 p-2 bg-light rounded border border-dashed small">
            <i class="bi bi-info-circle text-primary me-1"></i>
            <span>Mot de passe oublié ? <strong>Contactez votre professeur</strong> sur WhatsApp pour obtenir un code temporaire.</span>
        </div>

        <hr class="my-4">

        <!-- Lien Inscription -->
        <div class="text-center">
            <p class="text-muted small mb-0">Vous êtes un nouvel élève ?</p>
            <a href="{{ route('register') }}" class="btn btn-outline-dark btn-sm mt-2 w-100">
                <i class="bi bi-person-plus me-1"></i> Créer mon compte élève
            </a>
        </div>
    </form>
@endsection

