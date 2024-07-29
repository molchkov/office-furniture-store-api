<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function($property) {

            foreach ($property->values as $value) {
                if ($value->products()->exists()) {
                    throw new \Exception("Cannot delete property associated with products.");
                }
            }

            $property->values()->delete();
        });
    }

    public function values(): HasMany
    {
        return $this->hasMany(PropertyValue::class);
    }
}
