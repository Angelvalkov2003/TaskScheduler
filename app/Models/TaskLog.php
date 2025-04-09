<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLog extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'run_at', 'settings', 'run_outcome'];

    protected $casts = [
        'settings' => 'array',
        'run_outcome' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'tasklog_id');
    }
}
