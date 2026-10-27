<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_name', 'email', 'phone', 'address', 'notes', 'total', 'status'];
    protected $casts = ['total' => 'decimal:2'];

    public function items() { return $this->hasMany(OrderItem::class); }
}
