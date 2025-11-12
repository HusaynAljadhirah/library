<?php

namespace App\QueryBuilders;

use App\Models\Category;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryQueryBuilder
{
    public static function build()
    {
        return QueryBuilder::for(Category::class)
            ->withCount(['books'])
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('description'),
                AllowedFilter::callback('trashed', function ($q, $v) {
                    return match ($v) {
                        'only' => $q->onlyTrashed(),
                        'with' => $q->withTrashed(),
                        default => $q,
                    };
                }),
            ])
            ->allowedSorts([
                'name',
                'created_at',
                'books_count',
            ])
            ->defaultSort('name');
    }

}
