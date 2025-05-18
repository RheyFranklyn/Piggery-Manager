<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    /** @use HasFactory<\Database\Factories\ExpensesFactory> */
    use HasFactory;

     // If your table name is 'expenses', you can omit this line
    protected $table = 'expenses';

    protected $fillable = [
        'description',
        'amount',
        'category',
        'date',
        'pig_id',
        'batch_id',
        'is_recurring',
        'recurrence_type',
        'receipt_path',
    ];


}
