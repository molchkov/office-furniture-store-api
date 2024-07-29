<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'count',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function($product) {
            $product->values()->detach();
        });
    }

    public function values(): BelongsToMany
    {
        return $this->belongsToMany(PropertyValue::class, 'product_property_values', 'product_id', 'value_id');
    }
}
