<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'product_site_prices')
            ->using(ProductSitePrice::class)
            ->withPivot('price');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
