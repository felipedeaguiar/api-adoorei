<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;

/**
 *
 */
class SaleService
{

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param array $products
     * @return Sale
     * @throws \Exception
     */
    public function create(array $products): Sale
    {
        \DB::beginTransaction();

        $sale = $this->createSale();
        $this->addProductsToSale($sale, $products);

        \DB::commit();

        return $sale;
    }

    /**
     * @param $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll($filters = [])
    {
        $pageSize = 10;
        $query = Sale::query();

        if (array_key_exists('pageSize', $filters)) {
            $pageSize = $filters['pageSize'];
        }

        if (array_key_exists('withProducts', $filters)) {
            $query->with('products');
        }

        $sales = $query->paginate($pageSize);

        return $sales;
    }

    /**
     * @param string $uuid
     * @return bool
     */
    public function cancel(string $uuid): bool
    {
        $sale = Sale::where('uuid', $uuid)->firstOrFail();

        //vai refazer o estoque
        foreach ($sale->products as $product) {
            $this->productService->incrementStockQtd($product, $product->pivot->amount);
        }

        return $sale->delete();
    }

    /**
     * @param string $uuid
     * @return Sale
     */
    public function getByUuid(string $uuid, $withRelations = false, $withTrashed = false): Sale
    {
        $query = Sale::where('uuid', $uuid);

        if ($withRelations) {
            $query->with('products');
        }

        if ($withTrashed) {
            $query->withTrashed();
        }

        $sale = $query->firstOrFail();

        return $sale;
    }

    /**
     * @param Sale $sale
     * @return mixed
     */
    private function getTotalPrice(Sale $sale)
    {
        $totalAmount = $sale->products->sum(function ($product) {
            return $product->price * $product->pivot->amount;
        });

        return $totalAmount;
    }

    /**
     * @param array $products
     * @return void
     * @throws \Exception
     */
    private function validateProducts(array $products)
    {
        $validator = \Validator::make(['products' => $products], [
            'products' => 'required|array|min:1', // Verifica se é um array e tem pelo menos 1 elemento
            'products.*.id' => 'required|exists:products,id',
            'products.*.amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }

    /**
     * @return Sale
     */
    public function createSale():Sale
    {
        $sale = new Sale();
        $sale->save();

        return $sale;
    }

    /**
     * @param Sale $sale
     * @param array $products
     * @return void
     * @throws \Exception
     */
    public function addProductsToSale(Sale $sale, array $products)
    {
        $this->validateProducts($products);

        $productsData = [];

        foreach ($products as $productData) {
            $product = Product::findOrFail($productData['id']);

            $this->productService->decrementStockQtd($product, $productData['amount']);

            $amount = $productData['amount'];
            $productsData[$product->id] = ['amount' => $amount];
        }

        $sale->products()->attach($productsData);
        $sale->refresh();

        $this->updateSaleAmount($sale);
    }

    /**
     * @param Sale $sale
     * @return void
     */
    public function updateSaleAmount(Sale $sale): void
    {
        $sale->amount = $this->getTotalPrice($sale);
        $sale->save();
    }
}
