<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'host', 'value'];

    protected $casts = [
        'value' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
