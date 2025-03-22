<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRoleTeam extends Pivot
{
    protected $table = 'user_role_team';
}

