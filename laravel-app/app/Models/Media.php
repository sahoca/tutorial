<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['filename', 'path', 'mime_type', 'size', 'alt'];

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
