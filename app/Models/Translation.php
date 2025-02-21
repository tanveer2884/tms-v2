<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale', 
        'key', 
        'content'
    ];

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('key', 'LIKE', "%{$term}%")
                     ->orWhere('content', 'LIKE', "%{$term}%")
                     ->with('tags');
    }
    
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'translation_tag');
    }
}
