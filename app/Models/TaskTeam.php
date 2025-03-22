<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskTeam extends Pivot
{
    protected $table = 'task_team';
}
