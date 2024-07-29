<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function filterProducts(?array $filter = []): LengthAwarePaginator;

    public function getProductBySlug(string $slug): Product;

    public function getProductById(int $id): Product;

    public function createProduct(array $data): Product;

    public function updateProduct(int $id, array $data): Product;

    public function deleteProduct(int $id): void;
}
