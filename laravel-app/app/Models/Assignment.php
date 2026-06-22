<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Assignment extends Model
{
    protected $fillable = [
        'title', 'slug', 'authority', 'reference_no', 'assignment_date',
        'due_date', 'description', 'document_url', 'status', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'due_date' => 'date',
        'is_published' => 'boolean',
    ];

    public const AUTHORITIES = ['SPK', 'BDDK', 'TDUB', 'Diğer'];

    public const STATUSES = [
        'active' => 'Devam Ediyor',
        'completed' => 'Tamamlandı',
        'pending' => 'Beklemede',
    ];

    protected static function booted(): void
    {
        static::saving(function (Assignment $a) {
            if (empty($a->slug)) {
                $a->slug = Str::slug($a->title) . '-' . Str::lower(Str::random(4));
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
