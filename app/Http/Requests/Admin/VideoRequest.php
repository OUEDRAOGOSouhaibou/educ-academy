<?php

namespace App\Http\Requests\Admin;

use App\Models\Video;
use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'month_id' => ['required', 'exists:months,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'youtube_url' => ['required', 'string'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $youtubeUrl = $this->input('youtube_url');
            if ($youtubeUrl && ! Video::extractYoutubeId($youtubeUrl)) {
                $v->errors()->add(
                    'youtube_url',
                    'Le lien YouTube est invalide. Veuillez coller une URL valide (ex: https://youtu.be/... ou https://www.youtube.com/watch?v=...).'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'youtube_url.required' => 'Le lien de la vidéo YouTube est obligatoire.',
            'month_id.required' => 'Le mois de rattachement est obligatoire.',
        ];
    }
}

