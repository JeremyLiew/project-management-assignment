<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'budget_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user_mappings')->withPivot('role')->withTimestamps();
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

