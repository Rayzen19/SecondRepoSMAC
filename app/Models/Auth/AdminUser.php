<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminUser extends User
{
    protected $table = 'users';
    
    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'admin');
        });
    }
}
