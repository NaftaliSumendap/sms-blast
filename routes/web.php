<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;

Route::get('/', function () {
    return view('index');
});

Route::post('/send-sms', [SmsController::class, 'send'])->name('sms.send');

Route::post('/upload-sms', [SmsController::class, 'upload'])->name('sms.upload');
