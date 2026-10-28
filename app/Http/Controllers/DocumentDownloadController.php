<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    /**
     * Téléchargement forcé d'un document PDF sur l'appareil.
     */
    public function download(Document $document): StreamedResponse
    {
        $this->authorizeAccess($document);

        $downloadName = $this->buildDownloadFilename($document);

        return Storage::disk('local')->download($document->file_path, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Affichage du PDF directement dans le navigateur (inline).
     */
    public function view(Document $document): BinaryFileResponse
    {
        $this->authorizeAccess($document);

        $path = Storage::disk('local')->path($document->file_path);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this->buildDownloadFilename($document).'"',
        ]);
    }

    /**
     * Vérification stricte des autorisations d'accès au fichier.
     */
    protected function authorizeAccess(Document $document): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Administrateur : accès complet
        if ($user && $user->isAdmin()) {
            $this->ensureFileExists($document);
            return;
        }

        // 2. Élève : doit être approuvé
        if (! $user || ! $user->isApproved()) {
            abort(403, 'Votre compte n\'est pas autorisé à accéder aux documents pédagogiques.');
        }

        // 3. Le mois doit être publié et appartenir à l'année scolaire en cours
        $month = $document->month;
        if (! $month || ! $month->is_published || ! $month->schoolYear || ! $month->schoolYear->is_current) {
            abort(403, 'Ce document pédagogique n\'est pas publié pour le moment.');
        }

        $this->ensureFileExists($document);
    }

    /**
     * S'assure que le fichier physique existe réellement sur le disque privé.
     */
    protected function ensureFileExists(Document $document): void
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Le fichier PDF demandé est introuvable sur le serveur.');
        }
    }

    /**
     * Construit un nom de fichier propre et lisible pour l'enregistrement mobile.
     * Exemple : Tle-Maths-Cours-Continuite-et-limites.pdf
     */
    protected function buildDownloadFilename(Document $document): string
    {
        $cleanTitle = Str::slug($document->title);
        $monthName = $document->month ? Str::slug($document->month->name) : 'cours';
        $type = Str::slug($document->type->label());

        return "EducAcademy-Tle-Maths-{$monthName}-{$type}-{$cleanTitle}.pdf";
    }
}
