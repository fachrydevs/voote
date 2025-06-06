<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        // Jika ada path gambar di database, buat URL lengkapnya.
        // Jika tidak, kembalikan null atau gambar placeholder.
        if ($this->image) {
            // Storage::url() akan membuat URL publik yang benar
            // Contoh: http://localhost:8000/storage/candidates/namafile.jpg
            return url(Storage::url($this->image));
        }

        // Opsional: kembalikan URL gambar default jika tidak ada gambar
        return 'https://via.placeholder.com/400'; 
    }


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
