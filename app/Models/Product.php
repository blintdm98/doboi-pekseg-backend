<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $fillable = ['name', 'price', 'accounting_code'];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }


}
