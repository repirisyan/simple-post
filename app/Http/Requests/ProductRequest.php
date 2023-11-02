<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_product_id' => 'required',
            'nama' => 'required',
            'qty' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'product_code' => 'required|unique:products,product_code'
        ];
    }
}
