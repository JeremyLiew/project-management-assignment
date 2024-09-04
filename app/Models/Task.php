<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'user_id', 'project_id', 'expense_id','status','priority','due_date'];

    protected $casts = [
        'due_date' => 'date',
    ];

    // relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}

