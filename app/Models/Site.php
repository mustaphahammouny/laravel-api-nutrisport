<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = [
        'code',
        'name',
        'domain',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_site_prices')
            ->using(ProductSitePrice::class)
            ->withPivot('price');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
