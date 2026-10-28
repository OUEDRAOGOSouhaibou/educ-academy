<?php

use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\RegisterController;
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
        return redirect()->route('admin.students.index');
    })->name('dashboard');

    // Gestion des élèves
    Route::prefix('eleves')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::post('/{student}/autoriser', [StudentController::class, 'approve'])->name('approve');
        Route::post('/{student}/bloquer', [StudentController::class, 'block'])->name('block');
        Route::post('/{student}/debloquer', [StudentController::class, 'unblock'])->name('unblock');
        Route::post('/{student}/reinitialiser-mot-de-passe', [StudentController::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->name('destroy');
    });
});
