<?php

namespace App\Models;

use App\Search\IndexableDocument;
use App\Search\SearchIndexable;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model implements IndexableDocument
{
    use HasFactory;
    use SearchIndexable;

    protected $guarded = [];

    /**
     * {@inheritDoc}
     */
    public static function searchableAs(): array
    {
        return ['global_search'];
    }

    /**
     * {@inheritDoc}
     */
    protected static function newFactory(): Factory
    {
        return MovieFactory::new();
    }

    /**
     * {@inheritDoc}
     */
    public function toSearchableArray(): array
    {
        return [
            'global_search' => array_merge([
                'type' => 'movie',
                'id' => $this->getSearchKey(),
                'name' => $this->title,
                'author' => $this->director,
                'description' => $this->description,
                'created_at' => $this->created_at->timestamp,
            ]),
        ];
    }
}
