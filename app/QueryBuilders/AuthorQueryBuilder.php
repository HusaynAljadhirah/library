<?php

namespace App\QueryBuilders;

use App\Models\Author;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorQueryBuilder
{
    public static function build(Request $request): QueryBuilder
    {
        return QueryBuilder::for(Author::class)
            ->withCount(['books'])
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('bio'),
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

    public static function paginate(Request $request, int $perPage = 10)
    {
        return self::build($request)
            ->paginate($perPage)
            ->appends($request->query());
    }
}
