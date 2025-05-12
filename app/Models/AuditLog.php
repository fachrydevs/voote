<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'ip_address',
        'activity_type',
        'activity_description',
        'old_value',
        'new_value',
        'user_agent'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
