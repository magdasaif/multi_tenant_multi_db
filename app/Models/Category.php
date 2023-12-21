<?php

namespace App\Models;

use App\Trait\BelongToStoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, BelongToStoreTrait;
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
