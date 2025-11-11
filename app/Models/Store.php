<?php

namespace App\Models;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

use Illuminate\Database\Eloquent\Model;

class Store extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'contact_person',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_store');
    }
}
