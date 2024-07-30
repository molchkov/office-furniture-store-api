<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePropertyValueRequest;
use App\Http\Resources\PropertyValueResource;
use App\Repositories\PropertyValueRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class PropertyValueController extends Controller
{
    public function __construct(protected PropertyValueRepositoryInterface $propertyValueRepository)
    {
    }

    #[OA\Patch(
        path: '/api/v1/property-values/{id}',
        description: 'Returns updated value data',
        summary: 'Update existing value',
        security: [["passport"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'value', type: 'string'),
                        new OA\Property(property: 'slug', type: 'string')
                    ],
                    type: 'object'
                )
            ]
        ),
        tags: ['PropertyValue'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'PropertyValue id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ]
    )]
    #[OA\Response(response: Response::HTTP_ACCEPTED, description: 'Successful operation', content: new OA\JsonContent())]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated')]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Resource Not Found')]
    #[OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Unprocessable Content')]
    public function update(UpdatePropertyValueRequest $request, int $id): JsonResponse
    {
        return (new PropertyValueResource(
            $this->propertyValueRepository->updatePropertyValue(
                $id,
                $request->validated()
            )
        ))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    #[OA\Delete(
        path: '/api/v1/property-values/{id}',
        description: 'Deletes a record and returns no content',
        summary: 'Delete existing value',
        security: [["passport"=>[]]],
        tags: ['PropertyValue'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'PropertyValue id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ]
    )]
    #[OA\Response(response: Response::HTTP_NO_CONTENT, description: 'Successful operation', content: new OA\JsonContent())]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated')]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Resource Not Found')]
    #[OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Unprocessable Content')]
    public function destroy(int $id): JsonResponse
    {
        $this->propertyValueRepository->deletePropertyValue($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
