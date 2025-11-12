<?php

namespace App\QueryBuilders;

use App\Models\Borrow;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class BorrowQueryBuilder
{
    public static function build()
    {
        return QueryBuilder::for(Borrow::class)
            ->with(['user','book'])
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('book_id'),
                AllowedFilter::callback('borrowed_from', fn($q,$v)=>$q->whereDate('borrowed_at','>=',$v)),
                AllowedFilter::callback('borrowed_to', fn($q,$v)=>$q->whereDate('borrowed_at','<=',$v)),
                AllowedFilter::callback('returned', fn($q,$v)=> (int)$v ? $q->whereNotNull('returned_at') : $q->whereNull('returned_at')),
                AllowedFilter::callback('trashed', function ($q, $v) {
                    return match ($v) {
                        'only' => $q->onlyTrashed(),
                        'with' => $q->withTrashed(),
                        default => $q,
                    };
                }),
            ])
            ->allowedSorts([
                'borrowed_at',
                'due_at',
                'returned_at',
                'created_at',
            ])
            ->defaultSort('-borrowed_at');
    }

}
