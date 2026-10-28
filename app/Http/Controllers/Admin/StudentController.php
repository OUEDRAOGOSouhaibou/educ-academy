<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Liste et recherche des élèves avec filtres par statut.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $search = trim($request->query('q', ''));

        $query = User::where('role', UserRole::Student)->latest();

        if (! empty($search)) {
            $normalizedSearch = User::normalizePhone($search);
            $query->where(function ($q) use ($search, $normalizedSearch) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$normalizedSearch}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($statusFilter) && in_array($statusFilter, [UserStatus::Pending->value, UserStatus::Approved->value, UserStatus::Blocked->value])) {
            $query->where('status', $statusFilter);
        }

        $students = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', UserRole::Student)->count(),
            'pending' => User::where('role', UserRole::Student)->where('status', UserStatus::Pending)->count(),
            'approved' => User::where('role', UserRole::Student)->where('status', UserStatus::Approved)->count(),
            'blocked' => User::where('role', UserRole::Student)->where('status', UserStatus::Blocked)->count(),
        ];

        return view('admin.students.index', [
            'students' => $students,
            'stats' => $stats,
            'currentStatus' => $statusFilter,
            'search' => $search,
        ]);
    }

    /**
     * Valide l'inscription d'un élève.
     */
    public function approve(User $student): RedirectResponse
    {
        $student->status = UserStatus::Approved;
        $student->approved_at = now();
        $student->save();

        return back()->with('success', "Le compte de {$student->name} a été autorisé avec succès.");
    }

    /**
     * Bloque le compte d'un élève (déconnexion immédiate à la prochaine requête).
     */
    public function block(User $student): RedirectResponse
    {
        $student->status = UserStatus::Blocked;
        $student->save();

        return back()->with('warning', "Le compte de {$student->name} a été suspendu.");
    }

    /**
     * Débloque le compte d'un élève.
     */
    public function unblock(User $student): RedirectResponse
    {
        $student->status = UserStatus::Approved;
        $student->save();

        return back()->with('success', "Le compte de {$student->name} a été réactivé.");
    }

    /**
     * Réinitialise le mot de passe d'un élève et génère un code temporaire pour transmission WhatsApp.
     */
    public function resetPassword(User $student): RedirectResponse
    {
        // Mot de passe temporaire lisible de 8 caractères alphanumériques
        $tempPassword = strtolower(Str::random(4)) . rand(1000, 9999);

        $student->password = $tempPassword;
        $student->must_change_password = true;
        $student->save();

        return back()->with([
            'success' => "Mot de passe temporaire généré pour {$student->name}.",
            'temp_password' => $tempPassword,
            'temp_student_name' => $student->name,
            'temp_student_phone' => $student->phone,
        ]);
    }

    /**
     * Supprime définitivement un compte élève.
     */
    public function destroy(User $student): RedirectResponse
    {
        if ($student->isAdmin()) {
            return back()->withErrors(['error' => 'Impossible de supprimer un compte administrateur.']);
        }

        $name = $student->name;
        $student->delete();

        return back()->with('info', "Le compte de {$name} a été supprimé.");
    }
}

