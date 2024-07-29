<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PropertyValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'value',
        'slug',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function($property) {
            if ($property->products()->exists()) {
                throw new \Exception("Cannot delete value associated with products.");
            }
        });
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_property_values', 'value_id', 'product_id');
    }
}
