<?php

namespace App\QueryBuilders;

use App\Models\Book;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class BookQueryBuilder
{
    public static function build()
    {
        return QueryBuilder::for(Book::class)
            ->with(['author','category'])
            ->withCount(['borrows'])
            ->allowedFilters([
                AllowedFilter::partial('title'),
                AllowedFilter::exact('author_id'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::callback('published_from', fn($q,$v)=>$q->whereDate('published_date','>=',$v)),
                AllowedFilter::callback('published_to', fn($q,$v)=>$q->whereDate('published_date','<=',$v)),
                AllowedFilter::callback('has_borrows', fn($q,$v)=>$v ? $q->whereHas('borrows') : $q->whereDoesntHave('borrows')),
                AllowedFilter::callback('trashed', function ($q, $v) {
                    return match ($v) {
                        'only' => $q->onlyTrashed(),
                        'with' => $q->withTrashed(),
                        default => $q,
                    };
                }),
            ])
            ->allowedSorts([
                'title',
                'published_date',
                'created_at',
                'pages',
                'pdf_size',
                'borrows_count',
            ])
            ->defaultSort('-created_at');
    }

}
