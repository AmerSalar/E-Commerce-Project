<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected const array RELATIONS = ['categories', 'carts', 'orders'];

    protected $guarded = ['id'];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function carts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_items');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items');
    }

    public function scopeWithRelations(Builder $query, array|string|null $relations): Builder
    {
        if (empty($relations)) {
            return $query;
        }
        $requested = is_string($relations) ? explode(',', $relations) : (array) $relations;

        // this checks to see if the requested relations are allowed or remove them
        // (intersect the equalities)
        $result = array_intersect($requested, self::RELATIONS);
        return $query->with($result);
    }
}
