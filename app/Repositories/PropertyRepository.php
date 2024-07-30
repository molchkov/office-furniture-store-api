<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PropertyRepository implements PropertyRepositoryInterface
{
    public function __construct(protected Property $property)
    {
    }

    public function getAllPropertiesWithValues(): LengthAwarePaginator
    {
        return $this->property::with('values')->paginate(request()->per_page ?? 20);
    }

    public function getPropertyById(int $id): Property
    {
        return $this->property::findOrFail($id);
    }

    public function createProperty(array $data): Property
    {
        $property = Property::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name'])
        ]);

        if (!empty($data['values'])) {
            $values = collect($data['values'])
                ->map(function (array $value) use ($property) {
                    $value['slug'] = Str::slug($value['value']) . '_' . $property->slug;
                    return $value;
                });

            $property->values()->createMany($values);
        }

        return $property;
    }

    public function updateProperty(int $id, array $data): Property
    {
        $property = $this->getPropertyById($id);

        $property->update([
            'name' => $data['name'],
            'slug' => $data['slug']
        ]);

        if (!empty($data['values'])) {
            $values = collect($data['values'])
                ->map(function (array $value) use ($property) {
                    $value['slug'] = Str::slug($value['value']) . '_' . $property->slug;
                    return $value;
                });

            $property->values()->createMany($values);
        }

        return $property;
    }

    public function deleteProperty(int $id): void
    {
        $this->getPropertyById($id)->delete();
    }
}
