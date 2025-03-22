<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name', 'created_by', 'start_date', 'end_date', 'repeat', 'archived_at', 'is_active'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function settings()
    {
        return $this->hasMany(TaskSetting::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'task_team');
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class);
    }
}
