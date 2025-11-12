<?php

namespace App\QueryBuilders;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Centralized filters/sorts for the Users index.
 *
 * Usage in a controller:
 *   $users = UserQueryBuilder::build($request)
 *       ->paginate(10)
 *       ->appends($request->query());
 *   return UserResource::collection($users);
 */
class UserQueryBuilder
{
    /**
     * Build the query with allowed filters and sorts.
     */
    public static function build()
    {
        return QueryBuilder::for(User::class)
            ->with(['role'])
            ->allowedFilters([
                // Simple fields
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('role_id'),

                // Relationship-based filtering by role name
                AllowedFilter::callback('role', function ($query, $value) {
                    $query->whereHas('role', fn($q) => $q->where('name', $value));
                }),

                // Date range filtering
                AllowedFilter::callback('created_from', fn($q, $v) => $q->whereDate('created_at', '>=', $v)),
                AllowedFilter::callback('created_to', fn($q, $v) => $q->whereDate('created_at', '<=', $v)),

                // Global search across name/email
                AllowedFilter::callback('q', function ($q, $v) {
                    $q->where(function ($w) use ($v) {
                        $w->where('name', 'like', "%{$v}%")
                          ->orWhere('email', 'like', "%{$v}%");
                    });
                }),

                // Soft-delete visibility: filter[trashed]=only|with
                AllowedFilter::callback('trashed', function ($q, $v) {
                    return match ($v) {
                        'only' => $q->onlyTrashed(),
                        'with' => $q->withTrashed(),
                        default => $q,
                    };
                }),

                // Convenience: filter[only_active]=1 → status=true
                AllowedFilter::callback('only_active', fn($q, $v) => $q->where('status', true)),
            ])
            ->allowedSorts([
                'name',
                'email',
                'created_at',
                'updated_at',

                // Sort by borrows count (no custom Sort class required)
                AllowedSort::callback('borrows_count', function ($q, bool $descending) {
                    $q->withCount('borrows')
                      ->orderBy('borrows_count', $descending ? 'desc' : 'asc');
                }),
            ])
            ->defaultSort('-created_at');
    }


}
