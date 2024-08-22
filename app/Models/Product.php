<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category_product()
    {
        return $this->belongsTo(CategoryProduct::class);
    }

    public function getAllProduct()
    {
        return $this->with('category_product')->simplePaginate(10);
    }

    public function getListProduct()
    {
        return $this->without('category_product')->select('id', 'nama')->get();
    }

    public function storeProduct($data)
    {
        return $this->create($data);
    }

    public function getProduct($id)
    {
        return $this->with('category_product')->where('id', $id)->first();
    }

    public function updateProduct($id, $data)
    {
        return $this->find($id)->update($data);
    }

    public function destroyProduct($id)
    {
        return $this->destroy($id);
    }
}
