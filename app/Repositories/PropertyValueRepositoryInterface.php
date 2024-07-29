<?php

namespace App\Repositories;

use App\Models\PropertyValue;

interface PropertyValueRepositoryInterface
{
    public function getPropertyValueById(int $id): PropertyValue;

    public function updatePropertyValue(int $id, array $data): PropertyValue;

    public function deletePropertyValue(int $id): void;
}
