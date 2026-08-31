<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $exists = true;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function user(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (isset($attributes['user'])) {
                    $userData = is_string($attributes['user'])
                        ? json_decode($attributes['user'], true)
                        : $attributes['user'];

                    $user = new User();
                    $user->forceFill($userData);
                    $user->exists = true;

                    return $user;
                }

                return new User(['name' => 'Nepoznat autor']);
            }
        );
    }
}
