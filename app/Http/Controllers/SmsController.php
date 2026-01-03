<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Models\MessageBatch;
use App\Models\ConnectionSetting;
use App\Models\Contact;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class SmsController extends Controller
{
    /**
     * Helper function untuk memformat nomor HP ke format 628xxx
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', trim($phone));
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * 1. KIRIM PESAN MANUAL
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
            'due_date' => 'nullable|date',
        ]);

        $rawPhones = explode(',', $request->phone);
        $phones = [];
        foreach ($rawPhones as $rawPhone) {
            $formatted = $this->formatPhoneNumber($rawPhone);
            if (!empty($formatted)) {
                $phones[] = $formatted;
            }
        }
        $phones = array_unique($phones);

        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        if (!$config || empty($config->local_address)) {
            return back()->with('error', 'Koneksi gagal! Silakan atur Local Address di menu Beranda terlebih dahulu.');
        }

        $baseUrl = rtrim($config->local_address, '/'); 
        $url = $baseUrl . '/message'; 
        $username = $config->username;
        $password = $config->password;

        $isSuccess = false;
        $statusMsg = 'Gagal mengirim pesan.';

        try {
            $response = Http::withBasicAuth($username, $password)
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(30)
                ->post($url, ['phoneNumbers' => array_values($phones), 'message' => $request->message]);

            $isSuccess = $response->successful();
            $status = $isSuccess ? 'sent' : 'failed';
            $statusMsg = $isSuccess ? 'Berhasil terkirim ke antrian HP' : 'Gagal: ' . $response->body();

        } catch (\Exception $e) {
            $status = 'failed';
            $statusMsg = 'Gagal koneksi: ' . $e->getMessage();
        }

        foreach ($phones as $phone) {
            Message::create([
                'user_id' => Auth::id(),
                'phone' => $phone,
                'content' => $request->message,
                'due_date' => $request->due_date,
                'status' => $status
            ]);
        }

        return back()->with($isSuccess ? 'success' : 'error', $statusMsg);
    }

    /**
     * 2. PREVIEW EXCEL
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'template' => 'required|string',
            'kolom_nama' => 'required|string|max:1', // Kolom Nama
            'kolom_hp' => 'required|string|max:1',    // Kolom HP
            'kolom_jaminan' => 'nullable|string|max:1', // Kolom Jaminan
            'kolom_due_date' => 'nullable|string|max:1', // Kolom Jatuh Tempo
            'baris_mulai' => 'required|integer|min:1',
        ]);

        $request->session()->put('uploaded_filename', $request->file('file')->getClientOriginalName());

        $dataArray = Excel::toArray([], $request->file('file'))[0];
        
        $colNameIndex = ord(strtoupper($request->kolom_nama)) - 65; 
        $colHpIndex = ord(strtoupper($request->kolom_hp)) - 65; 
        $colJaminanIndex = $request->kolom_jaminan ? (ord(strtoupper($request->kolom_jaminan)) - 65) : null;
        $colDueIndex = $request->kolom_due_date ? (ord(strtoupper($request->kolom_due_date)) - 65) : null;
        $startRow = $request->baris_mulai - 1;

        $headers = $dataArray[0] ?? [];
        $headers = array_map(fn($h) => strtolower(trim((string)$h)), $headers);

        $previewData = [];
        
        foreach (array_slice($dataArray, $startRow) as $row) {
            if (!isset($row[$colHpIndex])) continue;
            
            $rawPhone = trim((string)$row[$colHpIndex]);
            $phone = $this->formatPhoneNumber($rawPhone);
            
            if (empty($phone)) continue;

            $name = trim((string)($row[$colNameIndex] ?? 'Tanpa Nama'));
            $collateral = isset($colJaminanIndex) ? trim((string)($row[$colJaminanIndex] ?? '')) : null;

            // Parse Tanggal
            $dueDate = null;
            if (!is_null($colDueIndex) && isset($row[$colDueIndex])) {
                $val = $row[$colDueIndex];
                try {
                    if (is_numeric($val)) {
                        $dueDate = Carbon::instance(Date::excelToDateTimeObject($val))->format('Y-m-d H:i:s');
                    } elseif (!empty($val)) {
                        $dueDate = Carbon::parse($val)->format('Y-m-d H:i:s');
                    }
                } catch (\Exception $e) { $dueDate = null; }
            }

            // Template Replacement
            $rowData = $row;
            if (count($rowData) < count($headers)) $rowData = array_pad($rowData, count($headers), null);
            $rowData = array_slice($rowData, 0, count($headers));
            $mappedData = array_combine($headers, $rowData);
            
            $messageContent = $request->template;
            foreach ($mappedData as $key => $value) {
                if (is_numeric($value) && preg_match('/(tanggal|date|tempo|waktu|tgl|due)/i', $key)) {
                    try { $value = Carbon::instance(Date::excelToDateTimeObject($value))->translatedFormat('d M Y'); } catch (\Exception $e) {}
                } elseif ($value instanceof \DateTime) {
                    $value = Carbon::instance($value)->translatedFormat('d M Y');
                }
                $messageContent = str_ireplace('{'.$key.'}', $value ?? '', $messageContent);
            }
            $messageContent = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $messageContent);

            $previewData[] = [
                'name' => $name, 
                'collateral' => $collateral, 
                'phone' => $phone,
                'message' => $messageContent,
                'due_date' => $dueDate
            ];
        }

        return view('pengiriman-terjadwal', compact('previewData'));
    }

    /**
     * 3. PROSES KIRIM BATCH (Setelah Preview)
     */
    public function processBatch(Request $request)
    {
        $request->validate([
            'phones' => 'required|array',
            'messages' => 'required|array',
            'names' => 'nullable|array',
            'collaterals' => 'nullable|array',
        ]);

        $phones = $request->input('phones');
        $messages = $request->input('messages');
        $names = $request->input('names');
        $collaterals = $request->input('collaterals');
        $dueDates = $request->input('due_dates');
        
        $batchName = $request->session()->get('uploaded_filename', 'Batch Upload ' . now()->format('d M Y H:i'));

        if (empty($phones)) return redirect()->route('tulis-pesan')->with('error', 'Data kosong.');

        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        if (!$config || empty($config->local_address)) return redirect()->route('tulis-pesan')->with('error', 'Koneksi gagal!');

        $baseUrl = rtrim($config->local_address, '/');
        $url = $baseUrl . '/message'; 
        $username = $config->username;
        $password = $config->password;

        // 1. BUAT FOLDER (BATCH) BARU
        $batch = MessageBatch::create(['user_id' => Auth::id(), 'batch_name' => $batchName]);

        $successCount = 0;
        $failCount = 0;

        foreach ($phones as $index => $phone) {
            $msgContent = $messages[$index] ?? '';
            $dueDate = $dueDates[$index] ?? null;
            $name = $names[$index] ?? 'Tanpa Nama';
            $collateral = $collaterals[$index] ?? null;

            // 2. SIMPAN/UPDATE KE BUKU TELEPON
            Contact::updateOrCreate(
                ['user_id' => Auth::id(), 'phone' => $phone],
                ['name' => $name, 'collateral' => $collateral]
            );

            // 3. KIRIM PESAN
            try {
                $response = Http::withBasicAuth($username, $password)
                    ->timeout(10)
                    ->post($url, ['phoneNumbers' => [$phone], 'message' => $msgContent]);

                $status = $response->successful() ? 'sent' : 'failed';
                if ($response->successful()) $successCount++; else $failCount++;
            } catch (\Exception $e) {
                $status = 'failed';
                $failCount++;
            }

            // 4. SIMPAN LOG PESAN
            Message::create([
                'user_id' => Auth::id(),
                'message_batch_id' => $batch->id,
                'phone' => $phone,
                'content' => $msgContent,
                'due_date' => $dueDate,
                'status' => $status
            ]);
        }

        $msg = "Batch '$batchName' selesai diproses. $successCount berhasil dikirim.";
        return redirect()->route('pengiriman-terjadwal')->with($successCount > 0 ? 'success' : 'error', $msg);
    }

    /**
     * 4. KIRIM ULANG SATUAN (RESEND MESSAGE)
     */
    public function resendMessage(Request $request, $id)
    {
        $msg = Message::where('user_id', Auth::id())->findOrFail($id);

        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        if (!$config || empty($config->local_address)) {
            return back()->with('error', 'Koneksi gagal! Cek pengaturan.');
        }

        $baseUrl = rtrim($config->local_address, '/');
        $url = $baseUrl . '/message';
        $username = $config->username;
        $password = $config->password;

        $isSuccess = false;
        try {
            $response = Http::withBasicAuth($username, $password)
                ->timeout(10)
                ->post($url, ['phoneNumbers' => [$msg->phone], 'message' => $msg->content]);

            if ($response->successful()) {
                $msg->update(['status' => 'sent']);
                $isSuccess = true;
            } else {
                $msg->update(['status' => 'failed']);
            }
        } catch (\Exception $e) {
            $msg->update(['status' => 'failed']);
        }

        return back()->with($isSuccess ? 'success' : 'error', $isSuccess ? 'Pesan berhasil dikirim ulang!' : 'Gagal kirim ulang.');
    }

    /**
     * 5. KIRIM ULANG MASSAL (RESEND BATCH)
     */
    public function resendBatch(Request $request, $batchId)
    {
        $batch = MessageBatch::where('user_id', Auth::id())->findOrFail($batchId);
        $messages = $batch->messages; 

        if ($messages->isEmpty()) return back()->with('error', 'Folder kosong.');

        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        if (!$config || empty($config->local_address)) return back()->with('error', 'Koneksi gagal! Cek pengaturan.');

        $baseUrl = rtrim($config->local_address, '/');
        $url = $baseUrl . '/message';
        $username = $config->username;
        $password = $config->password;

        $successCount = 0;
        $failCount = 0;
        
        $newDueDate = $request->input('new_due_date');
        $globalMessage = $request->input('global_message');
        
        // Buat array map kontak untuk Smart Template
        $contactMap = Contact::whereIn('phone', $messages->pluck('phone'))
                             ->pluck('name', 'phone');


        foreach ($messages as $msg) {
            $msgContent = $msg->content;
            
            // 1. Update Konten Pesan (Global Message)
            if (!empty($globalMessage)) {
                $finalContent = $globalMessage;
                $phoneClean = preg_replace('/[^0-9]/', '', $msg->phone);
                
                // Smart Template: Ganti {nama}
                $contactName = $contactMap->get($msg->phone) ?? $contactMap->get($phoneClean) ?? ''; 
                $finalContent = str_ireplace('{nama}', $contactName, $finalContent);
                
                $msgContent = $finalContent;
                $msg->content = $msgContent; // Simpan pesan baru
                $msg->save();
            }

            // 2. Update Tanggal (Perpanjang 3 hari jika tidak ada input tanggal baru)
            if ($newDueDate) {
                $msg->update(['due_date' => $newDueDate]);
            } else {
                // Perpanjang 3 hari dari due_date lama atau dari sekarang
                $newTime = ($msg->due_date) ? $msg->due_date->addDays(3) : Carbon::now()->addDays(3);
                $msg->update(['due_date' => $newTime]);
            }

            // 3. Kirim
            try {
                $response = Http::withBasicAuth($username, $password)
                    ->timeout(10)
                    ->post($url, ['phoneNumbers' => [$msg->phone], 'message' => $msgContent]);

                if ($response->successful()) {
                    $msg->update(['status' => 'sent']);
                    $successCount++;
                } else {
                    $msg->update(['status' => 'failed']);
                    $failCount++;
                }
            } catch (\Exception $e) {
                $msg->update(['status' => 'failed']);
                $failCount++;
            }
        }

        $msg = "Broadcast ulang selesai. $successCount berhasil, $failCount gagal.";
        return back()->with($successCount > 0 ? 'success' : 'error', $msg);
    }

    /**
     * 6. HAPUS BATCH (Digunakan saat Tolak Pengiriman Otomatis)
     */
    public function deleteBatch($id)
    {
        $batch = MessageBatch::where('user_id', Auth::id())->findOrFail($id);
        $batch->delete(); // Menghapus batch juga menghapus semua pesan terkait (cascade)

        return back()->with('success', "Folder '{$batch->batch_name}' dan semua pesannya berhasil dihapus.");
    }

    /**
     * 7. UPDATE & DELETE SATUAN
     */
    public function updateMessage(Request $request, $id) { 
        $msg = Message::where('user_id', Auth::id())->findOrFail($id);
        
        $msg->update([
            'content' => $request->message,
            'due_date' => $request->due_date
        ]);

        return back()->with('success', 'Jadwal dan pesan berhasil diperbarui.');
    }
    
    public function deleteMessage($id) { 
        $msg = Message::where('user_id', Auth::id())->findOrFail($id);
        $msg->delete();
        return back()->with('success', 'Pesan dihapus dari jadwal.');
    }
    
    /**
     * 8. RIWAYAT & JADWAL
     */
    public function history() { 
        $messages = Message::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10); 
        $stats = Message::where('user_id', Auth::id())->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        $chartData = ['sent' => $stats['sent'] ?? 0, 'pending' => $stats['pending'] ?? 0, 'failed' => $stats['failed'] ?? 0];
        $allBatches = MessageBatch::where('user_id', Auth::id())->withCount('messages as total_msg')->orderBy('created_at', 'desc')->get();
        return view('riwayat', compact('messages', 'chartData', 'allBatches'));
    }

    public function scheduled()
    {
        $userId = Auth::id();
        $batches = MessageBatch::where('user_id', $userId)
            ->with(['messages'])
            ->withCount(['messages as total_msg', 'messages as pending_msg' => function($q){
                $q->whereIn('status', ['pending', 'failed']); // Hitung pesan yang perlu diurus
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $manualMessages = Message::where('user_id', $userId)
            ->whereNull('message_batch_id') 
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->paginate(10); 

        return view('pengiriman-terjadwal', compact('batches', 'manualMessages'));
    }
    
    /**
     * 9. EXPORT LAPORAN
     */
    public function exportHistory(Request $request)
    {
        $userId = Auth::id();
        $type = $request->input('export_type', 'all'); 
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $batchIds = $request->input('batch_ids');
        
        $query = Message::select('messages.*', 
                                 'contacts.name as contact_name', 
                                 'contacts.collateral as contact_collateral',
                                 'message_batches.batch_name')
                        ->leftJoin('contacts', function($join) {
                            $join->on('messages.phone', '=', 'contacts.phone')
                                 ->where('contacts.user_id', '=', Auth::id());
                        })
                        ->leftJoin('message_batches', 'messages.message_batch_id', '=', 'message_batches.id')
                        ->where('messages.user_id', $userId)
                        ->orderBy('messages.created_at', 'desc');

        // Filter berdasarkan Tipe
        if ($type === 'manual') {
            $query->whereNull('messages.message_batch_id');
        } elseif ($type === 'batch') {
            $query->whereNotNull('messages.message_batch_id');
            if (!empty($batchIds)) {
                $query->whereIn('messages.message_batch_id', $batchIds);
            }
        } elseif ($type === 'all' && !empty($batchIds)) {
            // Jika memilih 'all' tapi memilih batch tertentu
             $query->whereIn('messages.message_batch_id', $batchIds);
        }

        // Filter berdasarkan Rentang Waktu
        if ($startDate) {
            $query->whereDate('messages.created_at', '>=', Carbon::parse($startDate));
        }
        if ($endDate) {
            $query->whereDate('messages.created_at', '<=', Carbon::parse($endDate));
        }

        $messages = $query->get();

        if ($messages->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diexport berdasarkan filter tersebut.');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Pegadaian_' . $type . '_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function() use ($messages) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['LAPORAN RIWAYAT PESAN PEGADAIAN - ' . now()->format('d M Y H:i:s')]);
            fputcsv($file, []);

            // Baris Header Kolom
            fputcsv($file, [
                'Waktu Kirim', 'Nama Nasabah', 'Nomor HP', 'Barang Jaminan', 
                'Jatuh Tempo', 'Status Pengiriman', 'Tipe Batch', 'Keterangan Pesan'
            ]);

            // Baris Data
            foreach ($messages as $msg) {
                fputcsv($file, [
                    $msg->created_at->format('Y-m-d H:i'),
                    $msg->contact_name ?? 'Tanpa Nama',
                    $msg->phone,
                    $msg->contact_collateral ?? '-',
                    $msg->due_date ? $msg->due_date->format('Y-m-d H:i') : '-',
                    ucfirst($msg->status),
                    $msg->message_batch_id ? $msg->batch_name : 'MANUAL',
                    $msg->content,
                ]);
            }
            
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}