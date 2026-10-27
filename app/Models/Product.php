<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['sku', 'name', 'category', 'cost', 'price', 'stock', 'image', 'active'];

    protected $casts = ['cost' => 'decimal:2', 'price' => 'decimal:2', 'active' => 'boolean'];
}
