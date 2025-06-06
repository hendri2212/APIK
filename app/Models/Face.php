<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'face_name',
        'user_id',
        'day'
    ];
}
