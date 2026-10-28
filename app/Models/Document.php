<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'month_id',
        'title',
        'description',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'file_size' => 'integer',
            'position' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Supprime proprement le fichier physique du disque lors de la suppression du modèle
        static::deleting(function (Document $document) {
            $document->deletePhysicalFile();
        });
    }

    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }

    public function scopeOfType(Builder $query, DocumentType|string $type): Builder
    {
        $value = $type instanceof DocumentType ? $type->value : $type;
        return $query->where('type', $value);
    }

    /**
     * Retourne la taille formatée lisible pour un élève (ex: 1.2 Mo ou 450 Ko).
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1, ',', ' ') . ' Mo';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', ' ') . ' Ko';
        }

        return $bytes . ' octets';
    }

    /**
     * Supprime le fichier associé sur le disque de stockage privé.
     */
    public function deletePhysicalFile(): bool
    {
        if ($this->file_path && Storage::disk('local')->exists($this->file_path)) {
            return Storage::disk('local')->delete($this->file_path);
        }

        return false;
    }
}
