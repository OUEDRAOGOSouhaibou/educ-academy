<?php

namespace App\Http\Requests\Admin;

use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $maxKb = config('app.pdf_max_size_mb', 20) * 1024;
        $isCreate = $this->isMethod('post') && ! $this->route('document');

        return [
            'month_id' => ['required', 'exists:months,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::enum(DocumentType::class)],
            'position' => ['nullable', 'integer', 'min:0'],
            'file' => [
                $isCreate ? 'required' : 'nullable',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                "max:{$maxKb}",
            ],
        ];
    }

    public function messages(): array
    {
        $maxMb = config('app.pdf_max_size_mb', 20);

        return [
            'file.required' => 'Le fichier PDF est obligatoire.',
            'file.mimes' => 'Le fichier doit être impérativement un document au format PDF (.pdf).',
            'file.mimetypes' => 'Le type de fichier téléversé n\'est pas un PDF valide.',
            'file.max' => "La taille du fichier ne peut pas dépasser {$maxMb} Mo.",
            'type.required' => 'Le type de document (cours, série, devoir, corrigé) est obligatoire.',
            'month_id.required' => 'Le mois de rattachement est obligatoire.',
        ];
    }
}

