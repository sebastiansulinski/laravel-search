<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\SearchIndexable;
use Workbench\Database\Factories\MovieFactory;

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
            'global_search' => [
                'type' => 'movie',
                'id' => $this->getSearchKey(),
                'name' => $this->title,
                'author' => $this->director,
                'description' => $this->description,
                'created_at' => $this->created_at->timestamp,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function shouldBeSearchable(): bool
    {
        return $this->searchable;
    }
}
