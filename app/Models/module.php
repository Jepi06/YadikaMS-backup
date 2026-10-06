<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['kode', 'nama'];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}
