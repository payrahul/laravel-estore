<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Controllers\Api\BaseController;

class ProductController extends BaseController

{
    /**
     * Display a listing of the resource.
     */




    public function index()
    {
        //
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
