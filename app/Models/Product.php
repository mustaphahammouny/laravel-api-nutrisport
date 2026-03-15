<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function sitePrices(): HasMany
    {
        return $this->hasMany(ProductSitePrice::class);
    }

    public function sitePrice(): HasOne
    {
        $currentSite = current_site();

        return $this->hasOne(ProductSitePrice::class)
            ->where('site_id', $currentSite->id);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
