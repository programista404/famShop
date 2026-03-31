<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = [
        'user_id',
        'name_member',
        'age',
        'gender',
        'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function allergyProfiles()
    {
        return $this->hasMany(AllergyProfile::class, 'member_id');
    }

    public function budget()
    {
        return $this->hasOne(Budget::class, 'member_id');
    }

    public function scanHistory()
    {
        return $this->hasMany(ScanHistory::class, 'member_id');
    }

    public function shoppingList()
    {
        return $this->hasMany(ShoppingList::class, 'member_id');
    }
}
