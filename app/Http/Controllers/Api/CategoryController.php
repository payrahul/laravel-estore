<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::select('id','name','slug','status')->get();

        if($categories->isEmpty()){
            return response()->json([
                'status'=>false,
                'message'=>'No categories found',
                'data'=> []    
            ],200);
        }
        return response()->json([
            'status'=>true,
            'message'=>'Categories fetched successfully',
            'data'=>CategoryResource::collection($categories)
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     */
   

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreCategoryRequest $request)
    {
        try{ 
            $category = Category::create($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'Category created successfully',
                'data'=>new CategoryResource($category)
            ], 201);

        } catch (\Exception $e){

            Log::error('Category creation failed',[
                'error'=> $e->getMessage()
            ]);

            return response()->json([
                'status'=>false,
                'message'=>'Something went wrong'
            ],500);          

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {    

        try{

            $category = Category::findOrFail($id);

            return response()->json([
                'status'=> true,
                'data'=> new CategoryResource($category)  
            ],200);
        }catch(ModelNotFoundException $e){

            return response()->json([
                'status'=> false,
                // 'data'=> $category
                'message'=>'Category not found'  
            ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{

             $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100|unique:categories,name,'.$id,
                'slug' => 'required|string|max:100|unique:categories,slug,'.$id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::findOrFail($id);

            $category->update([
                'name'=>$request->name,
                'slug'=>$request->slug,
            ]);

            return response()->json([
                'status'=> true,
                'message'=>'Category updated successfully',
                'data'=> new CategoryResource($category->fresh())  
            ],200);

        }catch (ModelNotFoundException $e){
            return response()->json([
                'status'=> false,
                // 'data'=> $category
                'message'=>'Category not found'  
            ],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);

                $category->delete();

            return response()->json([
                'status' => true,
                'message'=> 'Category deleted successfully'
            ],200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);

        }catch (\Exception $e) {
            //throw $th;

            Log::error('Category deletion failed',[
                'id'=>$id,
                'error'=> $e->getMessage()
            ]);

            return response()->json([
                'status'=> false,
                'message'=>'something went wrong'
            ],500);
        }
    }
}
