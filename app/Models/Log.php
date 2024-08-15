<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'changes'
    ];

    // Add other attributes or methods as needed
}
