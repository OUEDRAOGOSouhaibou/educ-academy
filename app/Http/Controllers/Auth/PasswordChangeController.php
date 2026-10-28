<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    /**
     * Affiche l'écran de changement de mot de passe obligatoire.
     */
    public function showChangeForm(): View
    {
        return view('auth.change-password');
    }

    /**
     * Enregistre le nouveau mot de passe choisi par l'utilisateur.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit comporter au moins 8 caractères.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = $request->input('password');
        $user->must_change_password = false;
        $user->save();

        if ($user->isAdmin()) {
            return redirect()->route('admin.students.index')->with(
                'success',
                'Votre mot de passe a été mis à jour avec succès.'
            );
        }

        return redirect()->route('student.dashboard')->with(
            'success',
            'Votre mot de passe a été mis à jour avec succès. Bienvenue sur Educ\'Academy !'
        );
    }
}
