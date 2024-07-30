<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\PropertyRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected PropertyRepositoryInterface $propertyRepository
    )
    {
    }

    #[OA\Get(
        path: '/api/v1/products',
        description: 'Returns list of products with all property values',
        summary: 'Get list of products with all property values',
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'values[]',
                description: 'Array of product property values to filter by',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string'))
            ),
            new OA\Parameter(
                name: 'min_price',
                description: 'Minimum price filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(format: 'float')
            ),
            new OA\Parameter(
                name: 'max_price',
                description: 'Maximum price filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(format: 'float')
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search by name',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
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
    #[OA\Response(response: Response::HTTP_OK, description: 'OK', content: new OA\JsonContent())]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filter = $request->only('min_price', 'max_price', 'search');
        if ($request->exists('values')) {
            $filter['values'] = $this->propertyRepository->getPropertiesByValues($request->get('values'));
        }
        return ProductResource::collection(
            $this->productRepository->filterProducts(
                $filter
            )
        );
    }

    #[OA\Post(
        path: '/api/v1/products',
        description: 'Returns created product data and all property values',
        summary: 'Create new product and sync with property values',
        security: [["passport"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'price', type: 'string', format: 'float'),
                        new OA\Property(property: 'count', type: 'integer'),
                        new OA\Property(property: 'values', type: 'array', items: new OA\Items(type: 'integer'))
                    ],
                    type: 'object'
                )
            ]
        ),
        tags: ['Product']
    )]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'Successful operation', content: new OA\JsonContent())]
    #[OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthenticated')]
    #[OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Unprocessable Content')]
    public function store(StoreProductRequest $request): JsonResponse
    {
        return (new ProductResource(
            $this->productRepository->createProduct(
                $request->validated()
            )
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/v1/products/{slug}',
        description: 'Returns product data and all values',
        summary: 'Get product information by slug',
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'Product slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ]
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'Successful operation', content: new OA\JsonContent())]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Resource Not Found')]
    public function show(string $slug): ProductResource
    {
        return new ProductResource(
            $this->productRepository->getProductBySlug(
                $slug
            )
        );
    }

    #[OA\Patch(
        path: '/api/v1/products/{id}',
        description: 'Returns updated product data and all property values',
        summary: 'Update the existing product and sync with property values',
        security: [["passport"=>[]]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                'application/json' => new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'slug', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'price', type: 'string', format: 'float'),
                        new OA\Property(property: 'count', type: 'integer'),
                        new OA\Property(property: 'values', type: 'array', items: new OA\Items(type: 'integer'))
                    ],
                    type: 'object'
                )
            ]
        ),
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Product id',
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
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        return (new ProductResource(
            $this->productRepository->updateProduct(
                $id,
                $request->validated()
            )
        ))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    #[OA\Delete(
        path: '/api/v1/products/{id}',
        description: 'Deletes a record and returns no content',
        summary: 'Delete existing product',
        security: [["passport"=>[]]],
        tags: ['Product'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Product id',
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
        $this->productRepository->deleteProduct($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
