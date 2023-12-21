<?php

namespace App\Models;

use App\Models\Store;
use App\Models\Category;
use App\Trait\BelongToStoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, BelongToStoreTrait;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
   
    //to add globalscope for product model
    // protected static function booted()
    // {
    //     static::addGlobalScope('store', function ($query) {
    //         $store=app()->make('store.active'); //return domain that accessed now 
    //         $query->where('store_id', $store->id);
    //     });
    // }  
    
}

