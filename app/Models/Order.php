<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['store_id', 'user_id', 'status', 'comment'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeSearch(Builder $query, string|null $search): Builder
    {
        return $query->when($search, function (Builder $query) use ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('status', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('store', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('orderDetails.product', fn($q) => $q->where('name', 'like', '%' . $search . '%'));;
            });
        });
    }

    public function scopeFilterStatus(Builder $query, string|null $status): Builder
    {
        return $query->when($status, function (Builder $query) use ($status) {
            $query->where('status', $status);
        });
    }
}
