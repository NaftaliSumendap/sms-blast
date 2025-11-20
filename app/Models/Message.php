<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message_batch_id', // <--- PASTIKAN BARIS INI ADA
        'phone',
        'content',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    // Relasi ke Batch (Folder)
    public function batch()
    {
        return $this->belongsTo(MessageBatch::class, 'message_batch_id');
    }
}