<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestImageGallery extends Model
{
    use HasFactory;

    protected $fillable = ['contest_id', 'image'];

}
