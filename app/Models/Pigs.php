<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pigs extends Model
{
    /** @use HasFactory<\Database\Factories\PigsFactory> */
    use HasFactory;

    protected $table = 'pigs';

    protected $fillable = [
        'pen_number',
        'date_bought',
        'cost',
        'breed',
        'expected_sell_date',
        'starting_weight',
        'current_weight',
    ];
}
