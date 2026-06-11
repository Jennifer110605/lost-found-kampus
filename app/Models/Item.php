<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'description',
        'location',
        'date_occurred',
        'photo',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_occurred' => 'date',
    ];

    /**
     * Accessor yang ikut dikirim ke frontend (Vue) saat di-serialize.
     */
    protected $appends = [
        'type_label',
        'photo_url',
    ];

    /**
     * Relasi: setiap item dimiliki oleh satu user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: satu item punya banyak komentar
     */
    public function claims()
    {
        return $this->hasMany(\App\Models\ClaimRequest::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Scope untuk filter berdasarkan tipe (lost/found)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope untuk pencarian barang
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('location', 'like', "%{$keyword}%");
        });
    }

    /**
     * Helper: label tipe barang dalam Bahasa Indonesia
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'lost' ? 'Barang Hilang' : 'Barang Ditemukan';
    }

    /**
     * Helper: URL foto barang
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/no-photo.png');
    }
}
