<?php

namespace App\Models\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class StudentUser extends User
{
    protected $table = 'users';
    
    protected static function booted(): void
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', 'student');
        });
    }
}
