<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', "Educ'Academy") }} - Authentification</title>

    <!-- PWA & Métadonnées Mobiles -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#095B86">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Educ'Academy">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page d-flex flex-column min-vh-100">

    <div class="container my-auto py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                
                <!-- Logo & Identité Educ'Academy -->
                <div class="text-center mb-4">
                    <a href="{{ url('/') }}" class="text-decoration-none d-inline-block">
                        @if(file_exists(public_path('images/logo.jpeg')))
                            <img src="{{ asset('images/logo.jpeg') }}" alt="Educ'Academy" class="auth-logo-badge mb-2">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center bg-ea-primary text-white rounded-circle mb-2" style="width: 76px; height: 76px;">
                                <i class="bi bi-mortarboard-fill fs-1 text-ea-accent"></i>
                            </div>
                        @endif
                        <h2 class="fw-bold text-dark mb-0">Educ'<span class="text-ea-accent">Academy</span></h2>
                    </a>
                    <p class="text-muted small mt-1 mb-0 fw-medium">Terminale &bull; Mathématiques</p>
                </div>

                <!-- Alertes flash globales -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                @endif

                <!-- Carte de formulaire élégante -->
                <div class="auth-card">
                    <div class="auth-card-top-bar"></div>
                    <div class="p-4 p-sm-5">
                        @yield('content')
                    </div>
                </div>

                <!-- Pied de page épuré -->
                <div class="text-center mt-4 text-muted small">
                    <p class="mb-0">&copy; {{ date('Y') }} Educ'Academy &bull; Plateforme d'apprentissage</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Bannière d'installation PWA Mobile -->
    <div id="pwa-install-banner" class="pwa-banner d-none shadow-lg py-2" role="alert">
        <div class="container d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="Logo Educ'Academy" class="pwa-banner-icon flex-shrink-0">
                <div>
                    <div class="fw-bold text-dark small mb-0">Installer l'application Educ'Academy</div>
                    <div class="text-muted smaller">Accès direct depuis votre écran d'accueil sans Play Store</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <button type="button" class="btn btn-ea-accent btn-sm fw-bold px-3 btn-install-pwa">
                    <i class="bi bi-download me-1"></i> Installer
                </button>
                <button type="button" class="btn-close btn-sm" id="btn-dismiss-pwa-banner" aria-label="Fermer"></button>
            </div>
        </div>
    </div>

</body>
</html>
