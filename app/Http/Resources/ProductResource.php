<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'slug'=>$this->slug,
            'category_id'=>$this->category_id,
            'price'=>$this->price,
            'stock'=>$this->stock,
            'image'=>$this->image ? url(Storage::url($this->image)): null,
            'status'=>$this->status,

            'category'    => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ],
        ];
    }
}
