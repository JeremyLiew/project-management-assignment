<?php

//Soo Yu Hung

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {

    use HasFactory;

    protected $fillable = ['name', 'description', 'budget_id', 'status', 'creator_id', 'completed_at'];

    public function users() {
        return $this->belongsToMany(User::class, 'project_user_mappings')->withPivot('role')->withTimestamps();
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function budget() {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
