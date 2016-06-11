<?php

namespace test\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name'];
    public $timestamps = false;
}
