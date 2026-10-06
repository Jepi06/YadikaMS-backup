<?php

namespace App\Models\Mapping;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GuruPembimbing extends Model
{
    protected $table    = 'guru_pembimbing';
    protected $fillable = ['user_id', 'nip', 'nama', 'no_hp', 'email'];

    public function penempatanPkl()
    {
        return $this->hasMany(PenempatanPkl::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}