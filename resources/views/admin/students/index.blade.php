@extends('layouts.app')

@section('content')
<div class="container">

    <!-- En-tête de la page -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-people-fill text-ea-primary me-2"></i> Gestion des Élèves
            </h2>
            <p class="text-muted small mb-0">Validez les inscriptions, gérez les accès et réinitialisez les mots de passe pour WhatsApp.</p>
        </div>
    </div>

    <!-- Notification mot de passe temporaire généré pour WhatsApp -->
    @if(session('temp_password'))
        <div class="card mb-4 shadow-sm" style="border: 1px solid rgba(221, 82, 14, 0.4); background-color: var(--ea-accent-light);">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="bi bi-whatsapp text-success me-2 fs-4"></i> Mot de passe temporaire généré !
                        </h5>
                        <p class="mb-1 text-secondary">
                            Élève : <strong>{{ session('temp_student_name') }}</strong> ({{ session('temp_student_phone') }})
                        </p>
                        <div class="d-inline-flex align-items-center bg-white border rounded px-3 py-1 my-1">
                            <span class="small text-muted me-2">Code :</span>
                            <span class="fs-5 fw-bold font-monospace text-ea-primary" id="tempCode">{{ session('temp_password') }}</span>
                        </div>
                        <p class="small text-muted mb-0 mt-1">
                            <i class="bi bi-exclamation-circle text-ea-accent me-1"></i> Ce code ne sera plus réaffiché. Transmettez-le à l'élève. Il devra obligatoirement changer de mot de passe à sa connexion.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success fw-bold px-3 shadow-sm" onclick="copyWhatsappMessage('{{ session('temp_student_name') }}', '{{ session('temp_password') }}', '{{ session('temp_student_phone') }}')">
                            <i class="bi bi-clipboard-check me-1"></i> Copier le message WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Cartes de statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-ea border-0 bg-white p-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-ea-primary-light text-ea-primary rounded-3 me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total élèves</div>
                        <div class="h4 fw-bold mb-0 text-ea-primary">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card card-ea border-0 {{ $stats['pending'] > 0 ? 'bg-ea-accent-light border border-ea-accent' : 'bg-white' }} p-3 shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-ea-accent-light text-ea-accent rounded-3 me-3">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">En attente</div>
                            <div class="h4 fw-bold mb-0 text-ea-accent">{{ $stats['pending'] }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.students.index', ['status' => 'approved']) }}" class="text-decoration-none">
                <div class="card card-ea border-0 bg-white p-3 shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-success bg-opacity-10 text-success rounded-3 me-3">
                            <i class="bi bi-check2-circle fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Autorisés</div>
                            <div class="h4 fw-bold mb-0 text-dark">{{ $stats['approved'] }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.students.index', ['status' => 'blocked']) }}" class="text-decoration-none">
                <div class="card card-ea border-0 bg-white p-3 shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="p-2 bg-danger bg-opacity-10 text-danger rounded-3 me-3">
                            <i class="bi bi-slash-circle fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Bloqués</div>
                            <div class="h4 fw-bold mb-0 text-dark">{{ $stats['blocked'] }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre d'outils : Recherche et Filtres -->
    <div class="card card-ea border-0 bg-white shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="status" value="{{ $currentStatus }}">

                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="q" value="{{ $search }}" class="form-control bg-light border-start-0" placeholder="Rechercher par nom ou téléphone...">
                        @if($search)
                            <a href="{{ route('admin.students.index', ['status' => $currentStatus]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                        <button class="btn btn-primary" type="submit">Rechercher</button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-7 d-flex flex-wrap gap-1 justify-content-md-end mt-2 mt-md-0">
                    <a href="{{ route('admin.students.index', ['q' => $search]) }}" class="btn btn-sm {{ empty($currentStatus) ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Tous ({{ $stats['total'] }})
                    </a>
                    <a href="{{ route('admin.students.index', ['status' => 'pending', 'q' => $search]) }}" class="btn btn-sm {{ $currentStatus === 'pending' ? 'btn-ea-accent' : 'btn-outline-secondary' }}">
                        En attente ({{ $stats['pending'] }})
                    </a>
                    <a href="{{ route('admin.students.index', ['status' => 'approved', 'q' => $search]) }}" class="btn btn-sm {{ $currentStatus === 'approved' ? 'btn-success fw-bold' : 'btn-outline-secondary' }}">
                        Autorisés ({{ $stats['approved'] }})
                    </a>
                    <a href="{{ route('admin.students.index', ['status' => 'blocked', 'q' => $search]) }}" class="btn btn-sm {{ $currentStatus === 'blocked' ? 'btn-danger fw-bold' : 'btn-outline-secondary' }}">
                        Bloqués ({{ $stats['blocked'] }})
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau / Liste des élèves -->
    <div class="card card-ea border-0 bg-white shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Élève</th>
                        <th>Téléphone</th>
                        <th>Statut</th>
                        <th>Inscription</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark">{{ $student->name }}</div>
                                @if($student->email)
                                    <div class="text-muted small"><i class="bi bi-envelope me-1"></i>{{ $student->email }}</div>
                                @endif
                                @if($student->must_change_password)
                                    <span class="badge bg-secondary small" style="font-size: 0.7rem;">Doit changer de mot de passe</span>
                                @endif
                            </td>
                            <td>
                                <a href="tel:{{ $student->phone }}" class="text-decoration-none fw-semibold text-ea-primary">
                                    <i class="bi bi-telephone text-muted me-1"></i>{{ $student->phone }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $student->status->badgeClass() }} px-2 py-1">
                                    {{ $student->status->label() }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ $student->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    @if($student->isPending())
                                        <form method="POST" action="{{ route('admin.students.approve', $student) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Autoriser l'élève">
                                                <i class="bi bi-check-lg me-1"></i> Autoriser
                                            </button>
                                        </form>
                                    @elseif($student->isApproved())
                                        <form method="POST" action="{{ route('admin.students.block', $student) }}" class="d-inline" onsubmit="return confirm('Suspendre l\'accès de cet élève ? Il sera déconnecté immédiatement.');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm" title="Bloquer l'élève">
                                                <i class="bi bi-slash-circle me-1"></i> Bloquer
                                            </button>
                                        </form>
                                    @elseif($student->isBlocked())
                                        <form method="POST" action="{{ route('admin.students.unblock', $student) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Débloquer l'élève">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Débloquer
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Réinitialiser mot de passe -->
                                    <form method="POST" action="{{ route('admin.students.reset-password', $student) }}" class="d-inline" onsubmit="return confirm('Générer un mot de passe temporaire pour cet élève ?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="Générer un mot de passe temporaire pour WhatsApp">
                                            <i class="bi bi-key"></i>
                                        </button>
                                    </form>

                                    <!-- Supprimer l'élève -->
                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="d-inline" onsubmit="return confirm('Supprimer définitivement cet élève ? Cette action est irréversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer le compte">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                                Aucun élève trouvé pour ces critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>

<script>
function copyWhatsappMessage(name, code, phone) {
    const message = `Bonjour ${name}, votre mot de passe temporaire pour accéder à Educ'Academy est : ${code}\nConnectez-vous sur ${window.location.origin}/login avec votre numéro de téléphone (${phone}). Vous serez invité à choisir un nouveau mot de passe.`;
    navigator.clipboard.writeText(message).then(() => {
        alert('Message WhatsApp copié dans le presse-papier ! Vous pouvez le coller directement dans votre conversation avec l\'élève.');
    });
}
</script>
@endsection
