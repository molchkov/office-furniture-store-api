<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
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

    public function getPropertiesByValues(array $values): array
    {
        return $this->property::with(['values' => function ($query) use ($values) {
            $query->whereIn('slug', $values);
        }])->whereHas('values', function (Builder $query) use ($values) {
            $query->whereIn('slug', $values);
        })->get()->pluck('values.*.slug')->toArray();
    }
}
