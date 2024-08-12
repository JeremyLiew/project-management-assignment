<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectUserMapping extends Pivot
{
    use HasFactory;

    protected $table = 'project_user_mappings';
}
