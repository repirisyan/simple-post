<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\CategoryProduct;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $categoryProduct, $product;

    public function __construct(){
        $this->categoryProduct = new CategoryProduct();
        $this->product = new Product();
    }

    public function index()
    {
        return Inertia::render('Product/Index',[
            'category_products' => $this->categoryProduct->getAllCategory(),
            'products' => $this->product->getAllProduct()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $this->product->storeProduct($request->validated());
            return to_route('product.index')->with('message',['success','Data berhasil disimpan']);
        } catch (Exception $e) {
            dd($e->getMessage());
            return to_route('product.index')->with('message',['error','Terjadi kesalahan saat menyimpan data']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            return response()->json($this->product->getProduct($id));
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        try {
            $this->product->updateProduct($id,$request->validated());
            return to_route('product.index')->with('message',['success','Data berhasil dubah']);
        } catch (Exception $e) {
            return to_route('product.index')->with('message',['error','Terjadi kesalahan saat mengubah data']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->product->destroyProduct($id);
            return to_route('product.index')->with('message',['success','Data berhasil dihapus']);
        } catch (Exception $e) {
            return to_route('product.index')->with('message',['error','Terjadi kesalahan saat menghapus data']);
        }
    }
}
