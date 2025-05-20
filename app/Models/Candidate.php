<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'election_id',
        'image',
        'order_number',
    ];

    public function election() {
        return $this->belongsTo(Election::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCount() {
        return $this->votes()->count();
    }

}
