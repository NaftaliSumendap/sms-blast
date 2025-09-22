<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsHistoryController;

Route::get('/', function () {
    return view('index');
});

Route::post('/send-sms', [SmsController::class, 'send'])->name('sms.send');

Route::post('/upload-sms', [SmsController::class, 'upload'])->name('sms.upload');

Route::get('/progress-status/{id}', [SmsController::class, 'progressStatus']);

Route::get('/riwayat', [SmsController::class, 'history'])->name('sms.history');

Route::get('/riwayat/{id}', [SmsHistoryController::class, 'show'])->name('history.show');


