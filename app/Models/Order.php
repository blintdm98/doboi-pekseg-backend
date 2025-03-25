<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['store_id', 'user_id', 'status'];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
