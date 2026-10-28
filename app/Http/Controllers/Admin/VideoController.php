<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoRequest;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;

class VideoController extends Controller
{
    /**
     * Enregistre une nouvelle vidéo.
     */
    public function store(VideoRequest $request): RedirectResponse
    {
        $youtubeId = Video::extractYoutubeId($request->input('youtube_url'));

        $video = Video::create([
            'month_id' => $request->input('month_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'youtube_id' => $youtubeId,
            'position' => $request->input('position') ?? 0,
        ]);

        return back()->with('success', "La vidéo « {$video->title} » a été ajoutée avec succès.");
    }

    /**
     * Met à jour une vidéo.
     */
    public function update(VideoRequest $request, Video $video): RedirectResponse
    {
        $youtubeId = Video::extractYoutubeId($request->input('youtube_url'));

        $video->update([
            'month_id' => $request->input('month_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'youtube_id' => $youtubeId,
            'position' => $request->input('position') ?? $video->position,
        ]);

        return back()->with('success', "La vidéo « {$video->title} » a été mise à jour.");
    }

    /**
     * Supprime une vidéo.
     */
    public function destroy(Video $video): RedirectResponse
    {
        $title = $video->title;
        $video->delete();

        return back()->with('info', "La vidéo « {$title} » a été supprimée.");
    }
}
