<?php

namespace App\QueryBuilders;

use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class RoleQueryBuilder
{
    public static function build()
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters([
                AllowedFilter::partial('name'),
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
                'updated_at',
            ])
            ->defaultSort('name');
    }

}
