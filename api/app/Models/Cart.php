<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_items');
    }
}
