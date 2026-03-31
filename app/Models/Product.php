<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'pr_name',
        'brand',
        'price',
        'image_url',
        'halal_status',
        'raw_ingredients',
    ];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'products_ingredients');
    }

    public function alternatives()
    {
        return $this->hasMany(AlternativeProduct::class);
    }
}
