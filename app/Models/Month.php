<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Month extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'name',
        'calendar_number',
        'position',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'calendar_number' => 'integer',
            'position' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Supprime proprement tous les fichiers PDF attachés avant la suppression du mois
        static::deleting(function (Month $month) {
            foreach ($month->documents as $document) {
                $document->delete();
            }
        });
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class)->orderBy('position');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->orderBy('position');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position');
    }
}
