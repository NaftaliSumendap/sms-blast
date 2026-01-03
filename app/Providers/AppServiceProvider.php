<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\MessageBatch;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan Bootstrap 5 untuk pagination tabel
        Paginator::useBootstrapFive();

        // Logika Global untuk Lonceng Notifikasi
        // Data ini akan dikirim ke 'layouts.app' setiap kali halaman dimuat
        View::composer('layouts.bar', function ($view) {
            $notifications = collect();
            $count = 0;

            if (Auth::check()) {
                $userId = Auth::id();
                $today = Carbon::today();

                // 1. AMBIL PESAN MANUAL (Individu)
                // Syarat: due_date hari ini DAN tidak punya batch_id (NULL)
                $manuals = Message::where('user_id', $userId)
                    ->whereNull('message_batch_id') 
                    ->whereDate('due_date', $today)
                    ->orderBy('due_date', 'asc')
                    ->get()
                    ->map(function ($msg) {
                        return (object) [
                            'id' => $msg->id,
                            'type' => 'manual',
                            'title' => $msg->phone, // Judul: Nomor HP
                            'desc' => Str::limit($msg->content, 40), // Deskripsi: Potongan pesan
                            'time' => $msg->due_date,
                            'link' => route('pengiriman-terjadwal') // Klik lari ke halaman jadwal
                        ];
                    });

                // 2. AMBIL BATCH/FOLDER (Grup dari Excel)
                // Syarat: Punya pesan di dalamnya yang due_date-nya hari ini
                $batches = MessageBatch::where('user_id', $userId)
                    ->whereHas('messages', function ($q) use ($today) {
                        $q->whereDate('due_date', $today);
                    })
                    ->with(['messages' => function($q) use ($today) {
                        // Ambil 1 pesan paling awal hari ini untuk menentukan jam notifikasi
                        $q->whereDate('due_date', $today)->orderBy('due_date', 'asc');
                    }])
                    ->withCount(['messages as today_count' => function ($q) use ($today) {
                        $q->whereDate('due_date', $today);
                    }])
                    ->get()
                    ->map(function ($batch) {
                        $firstMsg = $batch->messages->first();
                        $time = $firstMsg ? $firstMsg->due_date : now();

                        return (object) [
                            'id' => $batch->id,
                            'type' => 'batch',
                            'title' => $batch->batch_name, // Judul: Nama File Excel
                            'desc' => $batch->today_count . " pesan jatuh tempo hari ini", // Deskripsi: Jumlah pesan
                            'time' => $time,
                            'link' => route('pengiriman-terjadwal') // Klik lari ke halaman jadwal
                        ];
                    });

                // 3. GABUNGKAN & URUTKAN BERDASARKAN WAKTU
                $notifications = $manuals->concat($batches)->sortBy('time');
                $count = $notifications->count();
            }

            // Kirim variabel ke View
            $view->with('todayNotifications', $notifications);
            $view->with('notificationCount', $count);
        });
    }
}