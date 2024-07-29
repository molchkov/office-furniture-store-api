<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePropertyValueRequest;
use App\Http\Resources\PropertyValueResource;
use App\Repositories\PropertyValueRepositoryInterface;
use Illuminate\Http\JsonResponse;

class PropertyValueController extends Controller
{
    public function __construct(protected PropertyValueRepositoryInterface $propertyValueRepository)
    {
    }

    public function update(UpdatePropertyValueRequest $request, int $id): PropertyValueResource
    {
        return new PropertyValueResource(
            $this->propertyValueRepository->updatePropertyValue(
                $id,
                $request->validated()
            )
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->propertyValueRepository->deletePropertyValue($id);

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
