<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsHistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContactController;


Route::get('/login', function () {
    return view('auth.login');
})->name('login'); // Halaman untuk menampilkan form login

Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

Route::get('/', function () {
    return view('beranda');
});

Route::get('/tulis-pesan', function () {
    return view('tulis-pesan');
})->name('tulis-pesan');

Route::get('/beranda', [ConfigController::class, 'index'])->name('beranda');
    
    // Route untuk memproses penyimpanan data form
Route::post('/simpan-pengaturan', [ConfigController::class, 'update'])->name('config.update');

Route::get('/riwayat-pesan', [SmsController::class, 'history'])->name('riwayat-pesan');
    
Route::get('/pengiriman-terjadwal', [SmsController::class, 'scheduled'])->name('pengiriman-terjadwal');
    
    // Update & Delete Pesan Terjadwal (Fitur Edit di Modal)
Route::put('/pesan/{id}/update', [SmsController::class, 'updateMessage'])->name('sms.update');
Route::delete('/pesan/{id}/delete', [SmsController::class, 'deleteMessage'])->name('sms.delete');
Route::post('/pesan/{id}/resend', [SmsController::class, 'resendMessage'])->name('sms.resend');

Route::delete('/batch/{id}/delete', [SmsController::class, 'deleteBatch'])->name('sms.delete_batch');

    // RUTE BARU: Kirim Ulang MASSAL (Batch)
Route::post('/batch/{id}/resend', [SmsController::class, 'resendBatch'])->name('sms.resend_batch');

Route::get('/buku-telepon', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/buku-telepon/store', [ContactController::class, 'store'])->name('contacts.store');
Route::post('/buku-telepon/import', [ContactController::class, 'import'])->name('contacts.import');
Route::delete('/buku-telepon/delete-all', [ContactController::class, 'destroyAll'])->name('contacts.destroyAll');

    // ---->> TAMBAHKAN BARIS INI UNTUK PERBAIKI ERROR <<----
Route::delete('/buku-telepon/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');

Route::get('/riwayat-pesan', [SmsController::class, 'history'])->name('riwayat-pesan');

Route::get('/riwayat-export', [SmsController::class, 'exportHistory'])->name('sms.export');

Route::get('/syarat-ketentuan', function () {
    return view('syarat-ketentuan');
});

Route::get('/tentang-aplikasi', function () {
    return view('tentang');
});

Route::get('/bantuan', function () {
    return view('bantuan');
})->name('bantuan');

Route::get('/update-aplikasi', function () {
    return view('update');
});

Route::post('/send-sms', [SmsController::class, 'send'])->name('sms.send');

Route::post('/sms-preview', [SmsController::class, 'preview'])->name('sms.preview');

Route::post('/sms-process-batch', [SmsController::class, 'processBatch'])->name('sms.process_batch');

Route::post('/upload-sms', [SmsController::class, 'upload'])->name('sms.upload');

Route::get('/progress-status/{id}', [SmsController::class, 'progressStatus']);

Route::get('/riwayat', [SmsController::class, 'history'])->name('sms.history');

Route::get('/riwayat/{id}', [SmsHistoryController::class, 'show'])->name('history.show');

});


