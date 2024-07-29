<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Repositories\PropertyRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyController extends Controller
{
    public function __construct(protected PropertyRepositoryInterface $propertyRepository)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        return PropertyResource::collection(
            $this->propertyRepository->getAllPropertiesWithValues()
        );
    }

    public function store(StorePropertyRequest $request): PropertyResource
    {
        return new PropertyResource(
            $this->propertyRepository->createProperty(
                $request->validated()
            )
        );
    }

    public function show(int $id): PropertyResource
    {
        return new PropertyResource(
            $this->propertyRepository->getPropertyById($id)
        );
    }

    public function update(UpdatePropertyRequest $request, int $id)
    {
        return new PropertyResource(
            $this->propertyRepository->updateProperty(
                $id,
                $request->validated()
            )
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->propertyRepository->deleteProperty($id);

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
