<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'age',
        'profile_photo',
        'user_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function shoppingCarts()
    {
        return $this->hasMany(ShoppingCart::class);
    }

    public function scanHistory()
    {
        return $this->hasMany(ScanHistory::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function isAdmin(): bool
    {
        if ($this->id === 1) {
            return true;
        }

        if (! Schema::hasColumn($this->getTable(), 'user_type')) {
            return false;
        }

        return $this->user_type === 'admin';
    }

    public function displayUserType(): string
    {
        if ($this->isAdmin()) {
            return 'admin';
        }

        if (Schema::hasColumn($this->getTable(), 'user_type') && filled($this->user_type)) {
            return (string) $this->user_type;
        }

        return 'customer';
    }
}
