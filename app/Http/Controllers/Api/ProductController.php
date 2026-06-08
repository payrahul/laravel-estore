<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseController

{
    /**
     * Display a listing of the resource.
     */




    public function index()
    {
       $products = Product::with('category')->get();

       if($products->isEmpty()){
            return $this->errorResponse('No products found',200);
       }else{
        return $this->successResponse(ProductResource::collection($products),'Products',200);
       }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {

        try{

            $data = $request->validated();

            if($request->hasFile('image')){
                $data['image'] = $request->file('image')->store('products','public');
            }

            $product = Product::create($data);

            return $this->successResponse($product,'Product saved successfully,201');

        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return $this->errorResponse('Something went wrong',500);
        };       

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product),'Product fetched successfully',200);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateProductRequest $request, Product $product)
    {
    
         $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product->update($data);

        return $this->successResponse(
            new ProductResource($product->fresh()),
            'Product updated successfully',
            200
        );

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return $this->successResponse('Product deleted successfully',200);
    }
}
