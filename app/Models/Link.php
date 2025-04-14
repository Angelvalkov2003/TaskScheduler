<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Link extends Model
{
    protected $fillable = ['file_id', 'slug', 'email', 'password', 'first_used_at'];

    protected $dates = ['first_used_at'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($link) {
            // Generate a unique value if not provided
            if (empty($link->slug)) {
                do {
                    $link->slug = Str::random(16);
                } while (static::where('slug', $link->slug)->exists());
            }

            // Generate a unique password if not provided
            if (empty($link->password)) {
                do {
                    $link->password = Str::random(16);
                } while (static::where('password', $link->password)->exists());
            }
        });
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}