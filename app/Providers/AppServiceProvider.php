<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\MessageBatch; // Import Model Batch
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Logika Lonceng Notifikasi Global
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
                            'type' => 'manual',
                            'title' => $msg->phone, // Judul: Nomor HP
                            'desc' => $msg->content, // Deskripsi: Isi Pesan
                            'time' => $msg->due_date,
                            'link' => route('pengiriman-terjadwal') // Link ke halaman jadwal
                        ];
                    });

                // 2. AMBIL BATCH/FOLDER (Grup)
                // Syarat: Punya pesan yang due_date-nya hari ini
                $batches = MessageBatch::where('user_id', $userId)
                    ->whereHas('messages', function ($q) use ($today) {
                        $q->whereDate('due_date', $today);
                    })
                    ->with(['messages' => function($q) use ($today) {
                        // Ambil 1 pesan tercepat hari ini untuk patokan jam notifikasi
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
                            'type' => 'batch',
                            'title' => $batch->batch_name, // Judul: Nama File Excel
                            'desc' => $batch->today_count . " pesan jatuh tempo hari ini", // Deskripsi: Jumlah
                            'time' => $time,
                            'link' => route('pengiriman-terjadwal') // Link ke halaman jadwal
                        ];
                    });

                // 3. GABUNGKAN & URUTKAN BERDASARKAN JAM
                $notifications = $manuals->concat($batches)->sortBy('time');
                $count = $notifications->count();
            }

            $view->with('todayNotifications', $notifications);
            $view->with('notificationCount', $count);
        });
    }
}