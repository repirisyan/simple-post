<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryProductRequest;
use App\Models\CategoryProduct;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private $category_product;

    public function __construct(){
        $this->category_product = new CategoryProduct();
    }

    public function index()
    {
        return Inertia::render('CategoryProduct/Index',[
            'data' => $this->category_product->getAllCategory()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryProductRequest $request)
    {
        try {
            $this->category_product->storeCategory($request->validated());
            return to_route('category-product.index')->with('message',[
                'success','Data berhasil disimpan'
            ]);
        } catch (Exception $e) {
            return to_route('category-product.index')->with('message',[
                'error','Terjadi kesalahan saat menyimpan data'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            return response()->json($this->category_product->getCategory($id));
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryProductRequest $request, string $id)
    {
        try {
            $this->category_product->updateCategory($id,$request->validated());
            return to_route('category-product.index')->with('message',[
                'success','Data berhasil diubah'
            ]);
        } catch (Exception $e) {
            return to_route('category-product.index')->with('message',[
                'error','Terjadi kesalahan saat mengubah data'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->category_product->destroyCategory($id);
            return to_route('category-product.index')->with('message',['success','Data berhasil dihapus']);
        } catch (Exception $e) {
            return to_route('category-product.index')->with('message',['error','Terjadi kesalahan saat menghapus data']);
        }
    }
}
