<?php

//Soo Yu Hung

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {

    use HasApiTokens,
        HasFactory,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        5
    ];

    /**
     * Get the role of the user.
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role) {
        return $this->role === $role;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if the user has a specific role within a project.
     *
     * @param string $role
     * @param int $projectId
     * @return bool
     */
    public function hasProjectRole($role, $projectId) {
        return $this->projects()
                        ->where('project_id', $projectId)
                        ->wherePivot('role', $role)
                        ->exists();
    }

    /**
     * The projects that belong to the user.
     */
    public function projects() {
        return $this->belongsToMany(Project::class, 'project_user_mappings')->withPivot('role');
    }

    /**
     * Get the tasks for the user.
     */
    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
