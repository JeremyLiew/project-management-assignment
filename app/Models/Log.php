<?php
// Jeremy
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'changes',
        'log_level',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
