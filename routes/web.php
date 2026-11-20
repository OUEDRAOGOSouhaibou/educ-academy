<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\MonthController as AdminMonthController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\PendingNoticeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques / Accueil
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentification (Invités)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/connexion', [LoginController::class, 'login']);

    Route::get('/inscription', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authentification & Gestion de compte (Connectés)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [LoginController::class, 'logout'])->name('logout');

    // Écran d'attente pour les comptes pending
    Route::get('/en-attente-validation', [PendingNoticeController::class, 'show'])->name('student.pending');

    // Écran de changement obligatoire de mot de passe
    Route::get('/changement-mot-de-passe', [PasswordChangeController::class, 'showChangeForm'])->name('password.change');
    Route::post('/changement-mot-de-passe', [PasswordChangeController::class, 'update'])->name('password.change.update');

    // Consultation et téléchargement sécurisé des PDF
    Route::get('/documents/{document}/telecharger', [DocumentDownloadController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/consulter', [DocumentDownloadController::class, 'view'])->name('documents.view');

    // Espace Élève (accessible aux élèves autorisés)
    Route::prefix('espace-eleve')->name('student.')->group(function () {
        Route::get('/', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/mois/{month}', [StudentDashboardController::class, 'showMonth'])->name('months.show');
    });
});

/*
|--------------------------------------------------------------------------
| Espace Administration
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord principal
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 1. Gestion des élèves
    Route::prefix('eleves')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::post('/diffusion-push', [StudentController::class, 'broadcastPush'])->name('broadcast-push');
        Route::post('/{student}/autoriser', [StudentController::class, 'approve'])->name('approve');
        Route::post('/{student}/bloquer', [StudentController::class, 'block'])->name('block');
        Route::post('/{student}/debloquer', [StudentController::class, 'unblock'])->name('unblock');
        Route::post('/{student}/reinitialiser-mot-de-passe', [StudentController::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });

    // 2. Gestion des mois
    Route::prefix('mois')->name('months.')->group(function () {
        Route::get('/', [AdminMonthController::class, 'index'])->name('index');
        Route::post('/', [AdminMonthController::class, 'store'])->name('store');
        Route::get('/{month}', [AdminMonthController::class, 'show'])->name('show');
        Route::put('/{month}', [AdminMonthController::class, 'update'])->name('update');
        Route::post('/{month}/publication', [AdminMonthController::class, 'togglePublish'])->name('toggle-publish');
        Route::delete('/{month}', [AdminMonthController::class, 'destroy'])->name('destroy');
    });

    // 3. Gestion des vidéos
    Route::prefix('videos')->name('videos.')->group(function () {
        Route::post('/', [AdminVideoController::class, 'store'])->name('store');
        Route::put('/{video}', [AdminVideoController::class, 'update'])->name('update');
        Route::delete('/{video}', [AdminVideoController::class, 'destroy'])->name('destroy');
    });

    // 4. Gestion des documents PDF
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::post('/', [AdminDocumentController::class, 'store'])->name('store');
        Route::put('/{document}', [AdminDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [AdminDocumentController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Souscription & Gestion PWA Web Push
|--------------------------------------------------------------------------
*/
Route::get('/manifest.json', function () {
    return response(file_get_contents(public_path('manifest.json')), 200, [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
    ]);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Service-Worker-Allowed' => '/',
    ]);
})->name('pwa.sw');

Route::get('/offline.html', function () {
    return response(file_get_contents(public_path('offline.html')), 200, [
        'Content-Type' => 'text/html; charset=utf-8',
    ]);
})->name('pwa.offline');

Route::get('/push/public-key', [PushSubscriptionController::class, 'publicKey'])->name('push.public-key');
Route::post('/push/subscribe', [PushSubscriptionController::class, 'subscribe'])->name('push.subscribe');
Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'unsubscribe'])->name('push.unsubscribe');
Route::post('/push/test', [PushSubscriptionController::class, 'testNotification'])->middleware('auth')->name('push.test');


