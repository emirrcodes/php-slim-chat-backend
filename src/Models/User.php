<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['username'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function groupMemberships()
    {
        return $this->hasMany(GroupMember::class);
    }
}