<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', "Educ'Academy") }} - Authentification</title>

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

</body>
</html>
