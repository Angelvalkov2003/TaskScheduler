<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    protected $fillable = ['tasklog_id', 'path'];

    public function taskLog(): BelongsTo
    {
        return $this->belongsTo(TaskLog::class, 'tasklog_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }
}
