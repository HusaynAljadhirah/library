<?php

namespace App\QuerySorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

/**
 * Custom sorter that orders results by a relation's *_count column.
 *
 * Defaults to the `orders` relation when the sort property isn't suffixed with `_count`.
 *
 * Examples:
 * - AllowedSort::custom('orders_count', new SortByOrdersCount())
 *   -> will call withCount('orders') and order by `orders_count`.
 *
 * - AllowedSort::custom('borrows_count', new SortByOrdersCount())
 *   -> will call withCount('borrows') and order by `borrows_count`.
 */
class SortByOrdersCount implements Sort
{
    /**
     * Apply the sort to the query.
     */
    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        // Derive the relation name from the property ending in `_count`.
        // Fallback to `orders` if property doesn't follow the convention.
        $relation = str_ends_with($property, '_count')
            ? substr($property, 0, -6)
            : 'orders';

        $query->withCount($relation);

        $direction = $descending ? 'desc' : 'asc';
        $query->orderBy("{$relation}_count", $direction);
    }
}
