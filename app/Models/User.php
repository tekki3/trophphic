<?php

namespace App\Models;

use Trophphic\Core\TrophphicModel;

class User extends TrophphicModel
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'password'
    ];
} 