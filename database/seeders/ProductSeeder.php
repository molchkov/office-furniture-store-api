<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::factory(50)
            ->has(PropertyValue::factory()->count(3), 'values')
            ->create();

        $products = Product::factory(200)->create();

        foreach ($products as $product) {
            $randomProperties = fake()->randomElements($properties, rand(1, 5));
            foreach ($randomProperties as $property) {
                $value = fake()->randomElement($property->values);
                $product->values()->attach($value->id);
            }
        }
    }
}
