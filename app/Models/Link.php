<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    protected $fillable = ['file_id', 'value', 'email', 'password', 'first_used_at'];

    protected $dates = ['first_used_at'];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}