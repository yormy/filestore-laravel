<?php

namespace Yormy\FilestoreLaravel\Tests\Setup\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'email',
    ];

    public $timestamps = false;
}
