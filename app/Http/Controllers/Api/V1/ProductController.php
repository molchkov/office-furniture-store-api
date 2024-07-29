<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }
    #[OA\Get(
        path: '/api/v1/products',
        summary: 'Search products by filters',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'properties[]',
                description: 'Array of product properties to filter by',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string'))
            ),
            new OA\Parameter(
                name: 'minPrice',
                description: 'Minimum price filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(format: 'float')
            ),
            new OA\Parameter(
                name: 'maxPrice',
                description: 'Maximum price filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(format: 'float')
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search term',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
        ]
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK')]
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->productRepository->filterProducts(
                $request->only('properties', 'minPrice', 'maxPrice', 'search')
            )
        );
    }

    #[OA\Post(
        path: '/api/v1/products',
        summary: 'Create a new product',
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
        tags: ['Products']
    )]
    #[OA\Response(response: Response::HTTP_CREATED, description: 'OK', content: new OA\JsonContent())]
    public function store(StoreProductRequest $request): ProductResource
    {
        return new ProductResource(
            $this->productRepository->createProduct(
                $request->validated()
            )
        );
    }

    #[OA\Get(
        path: '/api/v1/products/{slug}',
        summary: 'Show a single product by slug',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'slug',
                description: 'The unique identifier for the product',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ]
    )]
    #[OA\Response(response: Response::HTTP_OK, description: 'OK')]
    public function show(string $slug): ProductResource
    {
        return new ProductResource(
            $this->productRepository->getProductBySlug(
                $slug
            )
        );
    }

    public function update(UpdateProductRequest $request, int $id): ProductResource
    {
        return new ProductResource(
            $this->productRepository->updateProduct(
                $id,
                $request->validated()
            )
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productRepository->deleteProduct($id);

        return response()->json(['message' => 'Deleted successfully.']);
    }
}
