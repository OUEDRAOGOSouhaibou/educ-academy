<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    /**
     * Enregistre un nouveau document PDF.
     */
    public function store(DocumentRequest $request): RedirectResponse
    {
        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $fileSize = $uploadedFile->getSize();

        // Stockage sur le disque privé 'local' (storage/app/private/documents)
        $filePath = $uploadedFile->store('documents', 'local');

        $document = Document::create([
            'month_id' => $request->input('month_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'file_path' => $filePath,
            'original_name' => $originalName,
            'file_size' => $fileSize,
            'position' => $request->input('position') ?? 0,
        ]);

        return back()->with('success', "Le document « {$document->title} » ({$document->formatted_size}) a été téléversé avec succès.");
    }

    /**
     * Met à jour un document (avec remplacement facultatif du fichier physique).
     */
    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $data = [
            'month_id' => $request->input('month_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'position' => $request->input('position') ?? $document->position,
        ];

        // Remplacement du fichier si un nouveau fichier a été fourni
        if ($request->hasFile('file')) {
            $document->deletePhysicalFile(); // Supprime l'ancien fichier sur le disque

            $newFile = $request->file('file');
            $data['file_path'] = $newFile->store('documents', 'local');
            $data['original_name'] = $newFile->getClientOriginalName();
            $data['file_size'] = $newFile->getSize();
        }

        $document->update($data);

        return back()->with('success', "Le document « {$document->title} » a été mis à jour.");
    }

    /**
     * Supprime un document PDF et son fichier physique sur le disque.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $title = $document->title;
        // Le hook static::deleting dans Document.php supprime automatiquement le fichier physique
        $document->delete();

        return back()->with('info', "Le document « {$title} » a été supprimé du disque et de la base.");
    }
}

