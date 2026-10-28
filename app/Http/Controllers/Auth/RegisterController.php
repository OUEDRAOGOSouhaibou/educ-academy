<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Affiche le formulaire d'inscription.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('student.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Enregistre un nouvel élève sur la plateforme.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // Protection contre les créations de comptes en masse (max 5 par minute par IP)
        $throttleKey = 'register:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'phone' => "Trop de tentatives d'inscription. Veuillez réessayer dans {$seconds} secondes.",
            ]);
        }
        RateLimiter::hit($throttleKey, 60);

        // Création sécurisée : role et status imposés côté serveur
        $user = new User([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email') ?: null,
            'password' => $request->input('password'),
        ]);

        $user->role = UserRole::Student;
        $user->status = UserStatus::Pending;
        $user->must_change_password = false;
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.pending')->with(
            'success',
            'Votre compte a été créé avec succès ! Votre accès est en attente d\'approbation par votre professeur.'
        );
    }
}
