<?php

// FOR FUTURE UPDATES, USED TO SAVE USER ADDRESS FOR BETTER UX
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
