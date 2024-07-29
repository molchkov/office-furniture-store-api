<?php

namespace App\Repositories;

use App\Models\PropertyValue;

class PropertyValueRepository implements PropertyValueRepositoryInterface
{
    public function __construct(protected PropertyValue $propertyValue)
    {
    }

    public function getPropertyValueById(int $id): PropertyValue
    {
        return $this->propertyValue::findOrFail($id);
    }

    public function updatePropertyValue(int $id, array $data): PropertyValue
    {
        $propertyValue = $this->getPropertyValueById($id);
        $propertyValue->update([
            'name' => $data['name'],
            'slug' => $data['slug']
        ]);

        return $propertyValue;
    }

    public function deletePropertyValue(int $id): void
    {
        $this->getPropertyValueById($id)->delete();
    }
}
