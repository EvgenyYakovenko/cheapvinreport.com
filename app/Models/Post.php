<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'thumbnail',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'translations',
    ];

    protected $casts = [
        'translations' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
