<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsHistory extends Model
{
    protected $fillable = [
        'type',
        'file_name',
        'template',
        'total',
        'success',
        'failed',
        'details',
    ];

    protected $casts = [
        'details' => 'array', // otomatis decode JSON ke array
    ];
}
