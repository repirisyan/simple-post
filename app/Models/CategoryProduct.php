<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function product(){
        return $this->hasMany(Product::class);
    }

    public function getAllCategory(){
        return $this->select('id','nama')->get();
    }

    public function storeCategory($data){
        return $this->create($data);
    }

    public function getCategory($category_id){
        return $this->where('id',$category_id)->first();
    }

    public function updateCategory($category_id,$data){
        return $this->find($category_id)->update($data);
    }

    public function destroyCategory($category_id){
        return $this->destroy($category_id);
    }
}
