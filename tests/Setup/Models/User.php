<?php

namespace Yormy\FilestoreLaravel\Tests\Setup\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'email',
        'encryption_key',
    ];

    public $timestamps = false;

    protected $casts = [
        'encryption_key' => 'encrypted',
    ];

    protected $hidden = [
        'encryption_key',
    ];
}
