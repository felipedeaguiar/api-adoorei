<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_create(): void
    {
        $saleService = app(\App\Services\SaleService::class);

        $sale = $saleService->createSale();

        $this->assertNotEmpty($sale->uuid);
        $this->assertEquals(0, $sale->amount);
    }

    public function test_create_with_empty_products()
    {
        $saleService = app(\App\Services\SaleService::class);

        $this->expectException(\Exception::class);
        //tentando criar venda
        $saleService->create([]);
    }

    public function test_create_with_invalid_products()
    {
        $products = [
            [
                'id' => 122323,
                'amount' => 1
            ]
        ];

        $this->expectException(\Exception::class);

        $saleService = app(\App\Services\SaleService::class);

        //tentando criar venda
        $saleService->create($products);
    }

    public function test_create_with_invalid_amount()
    {
        $products = [
            [
                'id' => 1,
                'amount' => 0
            ]
        ];

        $this->expectException(\Exception::class);

        $saleService = app(\App\Services\SaleService::class);

        //tentando criar venda
        $saleService->create($products);
    }

    public function test_create_with_products()
    {
        $products = [
            [
                'id' => 1,
                'amount' => 1
            ]
        ];

        $saleService = app(\App\Services\SaleService::class);

        //tentando criar venda
        $sale = $saleService->create($products);
        $this->assertNotEmpty($sale);

        $product = Product::find(1);
        $this->assertEquals($sale->amount, $product->price);
    }
}
