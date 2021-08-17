<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contest_id',
        'contest_no',
        'item_price',
        'item_quantity',
        'item_value',
    ];
}
