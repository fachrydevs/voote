<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates() {
        return $this->hasMany(Candidate::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function isActive() {
        $now = now();
        return $this->is_active && $now->between($this->start_date, $this->end_date); 
    }


}
