<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected Product $product)
    {
    }

    public function filterProducts(?array $filter = []): LengthAwarePaginator
    {
        $products = $this->product::with('values.property');

        if (!empty($filter)) {
            $products
                ->when(!empty($filter['properties']) && is_array($filter['properties']), function (Builder $query) use ($filter) {
                    $query->where(function (Builder $query) use ($filter) {
                        foreach ($filter['properties'] as $value) {
                            $query->orWhere(function (Builder $query) use ($value) {
                                $query->whereHas('values', function (Builder $query) use ($value) {
                                    $query->where('slug', $value);
                                });
                            });
                        }
                    });
                })
                ->when(!empty($filter['minPrice']), function (Builder $query) use ($filter) {
                    $query->where('price','>=', $filter['minPrice']);
                })
                ->when(!empty($filter['maxPrice']), function (Builder $query) use ($filter) {
                    $query->where('price','<=', $filter['maxPrice']);
                })
                ->when(!empty($filter['search']), function (Builder $query) use ($filter) {
                    $query->where('name','like', '%' . $filter['search'] . '%');
                });
        }

        return $products->paginate(40);
    }

    public function getProductBySlug(string $slug): Product
    {
        return $this->product::with('values.property')->where('slug', $slug)->firstOrFail();
    }

    public function getProductById(int $id): Product
    {
        return $this->product::findOrFail($id);
    }

    public function createProduct(array $data): Product
    {
        $product = $this->product::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'count' => $data['count'],
            'slug' => Str::slug($data['name'])
        ]);

        $product->values()->sync($data['values'] ?? []);

        return $product;
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);

        $product->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'count' => $data['count'],
            'slug' => $data['slug']
        ]);

        $product->values()->sync($data['values'] ?? []);

        return $product;
    }

    public function deleteProduct(int $id): void
    {
        $this->getProductById($id)->delete();
    }
}
