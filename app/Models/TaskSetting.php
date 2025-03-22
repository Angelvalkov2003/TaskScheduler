<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSetting extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'key', 'value'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}

