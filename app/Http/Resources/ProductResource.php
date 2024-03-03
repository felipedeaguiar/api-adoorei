<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->name,
            'price' => $this->price,
            'stock_qtd' => $this->stock_qtd,
            'amount' => $this->whenPivotLoaded('product_sale', function () {
                return $this->pivot->amount;
            }),
        ];
    }
}
