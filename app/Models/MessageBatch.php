<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageBatch extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'batch_name'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}