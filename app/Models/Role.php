<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = true;
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'description'];
}
