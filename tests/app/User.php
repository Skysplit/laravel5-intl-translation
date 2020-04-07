<?php

declare(strict_types=1);

namespace test\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}
