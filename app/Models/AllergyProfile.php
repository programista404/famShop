<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllergyProfile extends Model
{
    protected $fillable = [
        'member_id',//FK
        'allergy_type',
        'severity_level',
    ];

    public function member()
    {
        return $this->belongsTo(FamilyMember::class, 'member_id');
    }
}
