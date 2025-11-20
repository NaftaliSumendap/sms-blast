<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * Atribut yang bisa diisi secara massal (Mass Assignable).
     */
    protected $fillable = [
        'user_id',    // ID User pemilik kontak
        'name',       // Nama Kontak
        'phone',      // Nomor Telepon (Format 62xxx)
        'collateral', // Barang Jaminan (Emas, Laptop, BPKB, dll)
    ];

    /**
     * Relasi ke model User.
     * Setiap kontak dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}