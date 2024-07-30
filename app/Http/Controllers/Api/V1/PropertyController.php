<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Repositories\PropertyRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class PropertyController extends Controller
{
    public function __construct(protected PropertyRepositoryInterface $propertyRepository)
    {
    }

    #[OA\Get(
        path: '/api/v1/properties',
        description: 'Returns list of properties with all values',
        summary: 'Get list of properties with all values',
        tags: ['Property'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'per_page',
                description: 'Number of items per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            )
        ]
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'Successful operation', content: new OA\JsonContent())]
    public function index(): AnonymousResourceCollection
    {
        return PropertyResource::collection(
            $this->propertyRepository->getAllPropertiesWithValues()
        );
    }

    #[OA\Post(
        path: '/api/v1/properties',
        description: 'Returns created property data and all values',
        summary: 'Create new property and create new values',
        security: [["passport"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'values', type: 'array', items: new OA\Items(properties: [
                            'value' => new OA\Property(property: 'value', type: 'string')
                        ], type: 'object'))
                    ],
                    type: 'object'
                )
            ]
        ),
        tags: ['Property']
    )]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'Successful operation', content: new OA\JsonContent())]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated')]
    #[OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Unprocessable Content')]
    public function store(StorePropertyRequest $request): JsonResponse
    {
        return (new PropertyResource(
            $this->propertyRepository->createProperty(
                $request->validated()
            )
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/v1/properties/{id}',
        description: 'Returns property data and all values',
        summary: 'Get property information',
        tags: ['Property'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Property id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ]
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'Successful operation', content: new OA\JsonContent())]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Resource Not Found')]
    public function show(int $id): PropertyResource
    {
        return new PropertyResource(
            $this->propertyRepository->getPropertyById($id)
        );
    }

    #[OA\Patch(
        path: '/api/v1/properties/{id}',
        description: 'Returns updated property data and all values',
        summary: 'Update the existing property and create new values',
        security: [["passport"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'slug', type: 'string'),
                        new OA\Property(property: 'values', type: 'array', items: new OA\Items(properties: [
                            'value' => new OA\Property(property: 'value', type: 'string')
                        ], type: 'object'))
                    ],
                    type: 'object'
                )
            ]
        ),
        tags: ['Property'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Property id',
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
    public function update(UpdatePropertyRequest $request, int $id): JsonResponse
    {
        return (new PropertyResource(
            $this->propertyRepository->updateProperty(
                $id,
                $request->validated()
            )
        ))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    #[OA\Delete(
        path: '/api/v1/properties/{id}',
        description: 'Deletes a record and returns no content',
        summary: 'Delete existing property',
        security: [["passport"=>[]]],
        tags: ['Property'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Property id',
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
        $this->propertyRepository->deleteProperty($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
