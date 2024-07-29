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

class ProductController extends Controller
{
    public function __construct(protected ProductRepositoryInterface $productRepository)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->productRepository->filterProducts(
                $request->only('properties', 'minPrice', 'maxPrice', 'search')
            )
        );
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        return new ProductResource(
            $this->productRepository->createProduct(
                $request->validated()
            )
        );
    }

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
