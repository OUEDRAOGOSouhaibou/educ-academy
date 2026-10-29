@extends('layouts.app')

@section('content')
<div class="container">

    <!-- Bannière Hero -->
    <div class="row align-items-center py-4 py-lg-5">
        <div class="col-12 col-lg-7 text-center text-lg-start mb-4 mb-lg-0">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 justify-content-center justify-content-lg-start">
                <span class="badge badge-ea-primary px-3 py-2 rounded-pill fw-bold shadow-xs">
                    <i class="bi bi-mortarboard-fill me-1 text-ea-accent"></i> Terminale &bull; Mathématiques
                </span>

                <!-- Slogan officiel avec affichage gestuel -->
                <div class="ea-slogan-banner px-3 py-2 rounded-pill d-inline-flex align-items-center shadow-xs">
                    <span class="slogan-hand-gesture me-2" aria-hidden="true">✍️</span>
                    <span class="ea-slogan-text fst-italic fw-bold text-ea-primary small">
                        &laquo;&nbsp;<span class="ea-slogan-typewriter" data-slogan="Construis ton avenir et Prépare-toi à le Vivre">Construis ton avenir et Prépare-toi à le Vivre</span>&nbsp;&raquo;
                    </span>
                    <span class="ea-slogan-cursor">|</span>
                </div>
            </div>

            <h1 class="display-5 fw-bold text-dark mb-3">
                Révisez et réussissez vos <span class="text-ea-primary">Mathématiques</span>
            </h1>

            <p class="lead text-secondary mb-4" style="max-width: 600px;">
                La plateforme officielle pour regarder vos vidéos de cours sans saturer la mémoire de votre téléphone, et télécharger vos séries d'exercices avec corrigés types organisés mois par mois.
            </p>

            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-lg-start">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg fw-bold px-4 py-3 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-ea-accent btn-lg fw-bold px-4 py-3 shadow-sm">
                        <i class="bi bi-person-plus-fill me-2"></i> S'inscrire gratuitement
                    </a>
                @else
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-lg fw-bold px-4 py-3 shadow-sm">
                            <i class="bi bi-speedometer2 me-2"></i> Tableau de bord Professeur
                        </a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg fw-bold px-4 py-3 shadow-sm">
                            <i class="bi bi-play-circle-fill me-2"></i> Accéder à mes cours
                        </a>
                    @endif
                @endguest
            </div>

            <div class="mt-4 pt-2 text-muted small d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                <span><i class="bi bi-check-circle-fill text-success me-1"></i> Connexion par numéro de téléphone</span>
                <span><i class="bi bi-check-circle-fill text-success me-1"></i> Économie de données mobiles</span>
            </div>
        </div>

        <div class="col-12 col-lg-5 text-center">
            <div class="card card-ea border-0 bg-white p-4 shadow-sm text-start card-ea-top-accent">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-2 bg-ea-primary text-white rounded-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-collection-play-fill fs-4 text-white"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Educ'<span class="text-ea-accent">Academy</span></h5>
                        <small class="text-muted">Votre espace de travail de Terminale</small>
                    </div>
                </div>

                <div class="list-group list-group-flush border-top border-bottom my-2">
                    <div class="list-group-item px-0 py-3 d-flex align-items-center">
                        <i class="bi bi-phone text-ea-primary fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold text-dark">100% optimisé smartphone</div>
                            <small class="text-muted">Utilisable confortablement sur tout téléphone Android.</small>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-3 d-flex align-items-center">
                        <i class="bi bi-calendar-check text-ea-accent fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold text-dark">Organisation mois par mois</div>
                            <small class="text-muted">Retrouvez facilement les cours des mois passés pour vos révisions.</small>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-3 d-flex align-items-center">
                        <i class="bi bi-file-earmark-check text-success fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold text-dark">PDF & Corrigés officiels</div>
                            <small class="text-muted">Consultez en ligne ou téléchargez selon votre forfait.</small>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <span class="badge bg-light text-secondary border">Année scolaire 2026-2027</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Avantages -->
    <div class="py-5 border-top">
        <div class="text-center mb-5">
            <span class="badge badge-ea-accent fw-bold px-3 py-1 rounded-pill mb-2">Pourquoi Educ'Academy ?</span>
            <h2 class="h3 fw-bold text-dark">Une méthode adaptée aux réalités des élèves</h2>
            <p class="text-muted small">Fini le chaos des fichiers mélangés dans les discussions WhatsApp.</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card card-ea card-ea-hover border-0 bg-white h-100 p-4 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="bi bi-youtube fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Vidéos en ligne</h5>
                    <p class="text-secondary small mb-0">
                        Regardez les explications de cours directement en streaming sans saturer la mémoire interne de votre appareil.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card card-ea card-ea-hover border-0 bg-white h-100 p-4 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-ea-primary-light text-ea-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar3 fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Parcours par mois</h5>
                    <p class="text-secondary small mb-0">
                        D'Octobre à Juin, les cours restent archivés et accessibles pour vous permettre de réviser les chapitres antérieurs à tout moment.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card card-ea card-ea-hover border-0 bg-white h-100 p-4 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="bi bi-file-earmark-pdf fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Fiches PDF & Corrigés</h5>
                    <p class="text-secondary small mb-0">
                        Téléchargez les cours, devoirs surveillés et séries d'exercices avec affichage préalable du poids en mégaoctets.
                    </p>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card card-ea card-ea-hover border-0 bg-white h-100 p-4 shadow-sm text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-ea-accent-light text-ea-accent rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock-fill fs-2 text-ea-accent"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Espace sécurisé</h5>
                    <p class="text-secondary small mb-0">
                        Chaque élève dispose de son compte vérifié par le professeur. Pas besoin d'adresse e-mail compliquée : votre téléphone suffit.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bannière d'appel à l'action finale -->
    <div class="card border-0 ea-cta-banner p-4 p-md-5 my-4 text-center">
        <h3 class="fw-bold mb-2 text-white">Prêt à démarrer vos révisions ?</h3>
        <p class="text-white-50 mb-4" style="max-width: 550px; margin: 0 auto;">
            Créez votre compte en quelques secondes ou connectez-vous avec votre numéro de téléphone.
        </p>
        <div class="d-flex justify-content-center gap-2">
            @guest
                <a href="{{ route('register') }}" class="btn btn-ea-accent btn-lg fw-bold px-4 shadow-sm">
                    <i class="bi bi-person-plus me-1"></i> S'inscrire maintenant
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                </a>
            @else
                <a href="{{ route('student.dashboard') }}" class="btn btn-ea-accent btn-lg fw-bold px-4 shadow-sm">
                    <i class="bi bi-arrow-right-circle me-1"></i> Ouvrir mon espace cours
                </a>
            @endguest
        </div>
    </div>

</div>
@endsection
