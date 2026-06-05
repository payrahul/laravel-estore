<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100|unique:products,name',
            'slug' => 'required|string|max:100|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // 'status' => 'required|boolean',
        ];
    }

}
