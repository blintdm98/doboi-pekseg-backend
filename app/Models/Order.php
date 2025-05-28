<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['store_id', 'user_id', 'status', 'comment'];

    public function store() {
        return $this->belongsTo(Store::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
