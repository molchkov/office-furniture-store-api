<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PropertyRepositoryInterface
{
    public function getAllPropertiesWithValues(): LengthAwarePaginator;

    public function getPropertyById(int $id): Property;

    public function createProperty(array $data): Property;

    public function updateProperty(int $id, array $data): Property;

    public function deleteProperty(int $id): void;
}
