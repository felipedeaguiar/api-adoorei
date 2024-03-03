<?php

namespace App\Services;


use App\Models\Product;

/**
 *
 */
class ProductService
{
    /**
     * @param $filters
     * @return mixed
     */
    public function getAll($filters = [])
    {
        $pageSize = 10;
        $query = Product::query();

        if (array_key_exists('pageSize', $filters)) {
            $pageSize = $filters['pageSize'];
        }

        if (array_key_exists('available', $filters)) {
            $query->where('stock_qtd','>',0);
        }

        $products = $query->paginate($pageSize);

        return $products;
    }

    public function decrementStockQtd(Product $product, $amount): void
    {
        if ($product->stock_qtd >= $amount) {
            $product->stock_qtd -= $amount;
            $product->save();
        } else {
            throw new \Exception("We do not have the quantity of product {$product->id}");
        }
    }

    public function incrementStockQtd(Product $product, $amount):void
    {
        $product->stock_qtd += $amount;
        $product->save();
    }
}
