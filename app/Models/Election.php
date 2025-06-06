<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'start_date' => 'datetime:Y-m-d\TH:i:s',
        'end_date' => 'datetime:Y-m-d\TH:i:s',
        'is_active' => 'boolean'
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

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates() {
        return $this->hasMany(Candidate::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function isActive()
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }
    
    protected static function booted()
    {
        static::saving(function ($election) {
            $now = now();
            $election->is_active = $now->between($election->start_date, $election->end_date);
        });
    }


}
