<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanHistory extends Model
{
    protected $table = 'scan_history';

    protected $fillable = [
        'user_id',
        'member_id',
        'product_id',
        'match_status',
        'reason',
        'scan_date',
    ];

    protected $casts = [
        'scan_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(FamilyMember::class, 'member_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
