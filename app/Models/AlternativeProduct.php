<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlternativeProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'alternative_product_id',
        'created_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function alternative()
    {
        return $this->belongsTo(Product::class, 'alternative_product_id');
    }
}
