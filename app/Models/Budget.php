<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'member_id',
        'daily_budget',
        'weekly_budget',
        'monthly_budget',
        'daily_spent',
        'weekly_spent',
        'monthly_spent',
    ];

    public function member()
    {
        return $this->belongsTo(FamilyMember::class, 'member_id');
    }
}
