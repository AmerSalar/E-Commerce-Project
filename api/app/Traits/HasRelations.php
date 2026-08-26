<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method withRelations(array|string|null $relations) load many relations of a model automatically
 */
trait HasRelations
{
    public function scopeWithRelations(Builder $query, array|string|null $relations): Builder
    {
        if (empty($relations)) {
            return $query;
        }

        $allowed = property_exists($this, 'allowedRelations')
            ? $this->allowedRelations
            : [];

        $requested = is_string($relations) ? explode(',', $relations) : (array) $relations;

        // this checks to see if the requested relations are allowed or remove them
        // (intersect the equalities)
        $result = array_intersect(array_map('trim', $requested), $allowed);

        return $query->with($result);
    }

    public function loadRelations(array|string|null $relations)
    {
        if (empty($relations)) {
            return $this;
        }

        $allowed = property_exists($this, 'allowedRelations')
            ? $this->allowedRelations
            : [];

        $requested = is_string($relations) ? explode(',', $relations) : (array) $relations;

        $values = array_values(array_intersect(array_map('trim', $requested), $allowed));

        return empty($values)
            ? $this
            : $this->loadMissing($values);
    }
}
