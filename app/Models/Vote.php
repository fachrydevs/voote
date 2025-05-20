<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\EncryptionService;


class Vote extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'election_id',
        'candidate_id',
        'user_id',
        'encrypted_user_id',
        'encrypted_vote_data',
    ];

    public function election() {
        return $this->belongsTo(Election::class);
    }

    public function candidate() {
        return $this->belongsTo(Candidate::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public static function boot() {
        parent::boot();
        static::creating(function ($vote) {
            $encryiptionService = app(EncryptionService::class);

            $vote->encrypted_user_id = $encryiptionService->encrypt($vote->user_id);
            $vote->encrypted_vote_data = $encryiptionService->encrypt(json_encode([
                'election_id' => $vote->election_id,
                'candidate_id' => $vote->candidate_id,
                'timestamp' => now()->toIso8601String(),
            ]));
        });
    }
}
