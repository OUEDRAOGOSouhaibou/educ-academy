<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PendingNoticeController extends Controller
{
    /**
     * Affiche l'écran d'attente pour les élèves dont le compte n'a pas encore été validé.
     */
    public function show(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isApproved()) {
            return redirect()->route('student.dashboard');
        }

        return view('student.pending', [
            'student' => $user,
        ]);
    }
}

