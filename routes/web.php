<?php

use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\MonthController as AdminMonthController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\Student\PendingNoticeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques / Accueil
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
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
    Route::get('/espace-eleve', function () {
        return view('student.dashboard');
    })->name('student.dashboard');
});

/*
|--------------------------------------------------------------------------
| Espace Administration
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.months.index');
    })->name('dashboard');

    // 1. Gestion des élèves
    Route::prefix('eleves')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
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
