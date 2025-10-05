<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'source_id',
        'title',
        'excerpt',
        'content',
        'image_url',
        'url',
        'canonical_url',
        'author_id',
        'category_id',
        'language',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    // Relations
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class)->withDefault([
            'name' => 'Unknown',
        ]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'name' => 'Uncategorized',
            'slug' => 'uncategorized',
        ]);
    }
}
