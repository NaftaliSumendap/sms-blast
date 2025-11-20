<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Message;
use App\Models\MessageBatch;
use App\Models\ConnectionSetting;
use App\Models\Contact;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SmsController extends Controller
{
    /**
     * 1. KIRIM PESAN MANUAL
     * Mengirim pesan ke satu atau banyak nomor sekaligus (Pesan Sama).
     */
    public function sendManual(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'message' => 'required',
            'due_date' => 'nullable|date',
        ]);

        // Bersihkan & Format Nomor
        $phones = array_map('trim', explode(',', $request->phone));
        $phones = array_filter($phones, fn($p) => !empty($p));
        // Format ke 62xxx (hapus 0 depan, hapus +)
        $phones = array_map(function($p) {
            $p = preg_replace('/[^0-9]/', '', $p);
            if (str_starts_with($p, '08')) {
                $p = '62' . substr($p, 2);
            }
            return $p;
        }, $phones);

        // Ambil Config dari Database
        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        if (!$config || empty($config->local_address)) {
            return back()->with('error', 'Koneksi gagal! Silakan atur Local Address di menu Beranda terlebih dahulu.');
        }

        $baseUrl = rtrim($config->local_address, '/');
        $url = $baseUrl . '/message'; // Endpoint untuk kirim
        $username = $config->username;
        $password = $config->password;

        // Payload (Batch Send untuk Manual)
        $payload = [
            'phoneNumbers' => array_values($phones),
            'message'      => $request->message,
        ];

        try {
            $response = Http::withBasicAuth($username, $password)
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(30)
                ->post($url, $payload);

            $isSuccess = $response->successful();
            $status = $isSuccess ? 'sent' : 'failed';
            $statusMsg = $isSuccess ? 'Berhasil dikirim ke server HP' : 'Gagal: ' . $response->body();

        } catch (\Exception $e) {
            $isSuccess = false;
            $status = 'failed';
            $statusMsg = 'Gagal koneksi: ' . $e->getMessage();
        }

        // Simpan Log ke Database
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
            'kolom_hp' => 'required|string',
            'kolom_nama' => 'required|string',
            // 'kolom_jaminan' opsional
            'baris_mulai' => 'required|integer|min:1',
        ]);

        $request->session()->put('uploaded_filename', $request->file('file')->getClientOriginalName());

        $dataArray = Excel::toArray([], $request->file('file'))[0];
        
        $colHpIndex = ord(strtoupper($request->kolom_hp)) - 65; 
        $colNameIndex = ord(strtoupper($request->kolom_nama)) - 65;
        $colJaminanIndex = $request->kolom_jaminan ? (ord(strtoupper($request->kolom_jaminan)) - 65) : null;
        $colDueIndex = $request->kolom_due_date ? (ord(strtoupper($request->kolom_due_date)) - 65) : null;
        
        $startRow = $request->baris_mulai - 1;

        $headers = $dataArray[0] ?? [];
        $headers = array_map(fn($h) => strtolower(trim((string)$h)), $headers);

        $previewData = [];
        
        foreach (array_slice($dataArray, $startRow) as $row) {
            if (!isset($row[$colHpIndex])) continue;
            $rawPhone = trim((string)$row[$colHpIndex]);
            if (empty($rawPhone)) continue;

            // Format Nomor
            $phone = preg_replace('/[^0-9]/', '', $rawPhone);
            if (str_starts_with($phone, '08')) {
                $phone = '62' . substr($phone, 2);
            }

            // Ambil Nama
            $name = isset($row[$colNameIndex]) ? trim((string)$row[$colNameIndex]) : 'Tanpa Nama';

            // Ambil Jaminan
            $collateral = null;
            if (!is_null($colJaminanIndex) && isset($row[$colJaminanIndex])) {
                $collateral = trim((string)$row[$colJaminanIndex]);
            }

            // Parse Tanggal Jatuh Tempo
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
                    try {
                        $value = Carbon::instance(Date::excelToDateTimeObject($value))->translatedFormat('d M Y');
                    } catch (\Exception $e) {}
                } elseif ($value instanceof \DateTime) {
                    $value = Carbon::instance($value)->translatedFormat('d M Y');
                }
                
                $messageContent = str_replace('{'.$key.'}', $value ?? '', $messageContent);
            }
            $messageContent = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $messageContent);

            $previewData[] = [
                'name' => $name,
                'phone' => $phone,
                'collateral' => $collateral, // Kirim jaminan ke preview
                'message' => $messageContent,
                'due_date' => $dueDate
            ];
        }

        return view('pengiriman-terjadwal', compact('previewData'));
    }

    /**
     * 3. PROSES KIRIM BATCH
     */
    public function processBatch(Request $request)
    {
        $phones = $request->input('phones', []);
        $names = $request->input('names', []);
        $collaterals = $request->input('collaterals', []);
        $messages = $request->input('messages', []);
        $dueDates = $request->input('due_dates', []);

        if (empty($phones)) return redirect()->route('tulis-pesan')->with('error', 'Data kosong.');

        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        if (!$config || empty($config->local_address)) return redirect()->route('tulis-pesan')->with('error', 'Koneksi gagal!');

        $baseUrl = rtrim($config->local_address, '/');
        $url = $baseUrl . '/message';
        $username = $config->username;
        $password = $config->password;

        $batchName = $request->session()->get('uploaded_filename', 'Upload ' . now()->format('d M Y H:i'));
        $batch = MessageBatch::create([
            'user_id' => Auth::id(),
            'batch_name' => $batchName
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($phones as $index => $phone) {
            $msgContent = $messages[$index] ?? '';
            $dueDate = $dueDates[$index] ?? null;
            $name = $names[$index] ?? 'Tanpa Nama';
            $collateral = $collaterals[$index] ?? null;

            // Update Kontak
            Contact::updateOrCreate(
                ['user_id' => Auth::id(), 'phone' => $phone],
                ['name' => $name, 'collateral' => $collateral]
            );

            try {
                $response = Http::withBasicAuth($username, $password)
                    ->timeout(10)
                    ->post($url, [
                        'phoneNumbers' => [$phone], 
                        'message' => $msgContent
                    ]);

                if ($response->successful()) {
                    $status = 'sent';
                    $successCount++;
                } else {
                    $status = 'failed';
                    $failCount++;
                }
            } catch (\Exception $e) {
                $status = 'failed';
                $failCount++;
            }

            Message::create([
                'user_id' => Auth::id(),
                'message_batch_id' => $batch->id,
                'phone' => $phone,
                'content' => $msgContent,
                'due_date' => $dueDate,
                'status' => $status
            ]);
        }

        $msg = "Batch selesai. $successCount sukses, $failCount gagal.";
        
        // Tambahkan Session 'status' agar Toast Pop-up muncul
        if ($successCount > 0) {
            $request->session()->flash('status', "Berhasil memproses Excel! $successCount pesan terkirim.");
        }

        return redirect()->route('pengiriman-terjadwal')->with($successCount > 0 ? 'success' : 'error', $msg);
    }

    /**
     * 4. KIRIM ULANG SATUAN
     */
    public function resendMessage($id)
    {
        $msg = Message::where('user_id', Auth::id())->findOrFail($id);
        $config = ConnectionSetting::where('user_id', Auth::id())->first();
        
        if (!$config || empty($config->local_address)) return back()->with('error', 'Koneksi gagal!');

        $baseUrl = rtrim($config->local_address, '/');
        $url = $baseUrl . '/message';
        
        try {
            $response = Http::withBasicAuth($config->username, $config->password)
                ->timeout(15)
                ->post($url, [
                    'phoneNumbers' => [$msg->phone],
                    'message' => $msg->content
                ]);

            if ($response->successful()) {
                $msg->update(['status' => 'sent']);
                return back()->with('success', 'Pesan berhasil dikirim ulang.');
            } else {
                $msg->update(['status' => 'failed']);
                return back()->with('error', 'Gagal mengirim ulang: ' . $response->body());
            }

        } catch (\Exception $e) {
            $msg->update(['status' => 'failed']);
            return back()->with('error', 'Gagal koneksi ke HP: ' . $e->getMessage());
        }
    }

    /**
     * 5. KIRIM ULANG BATCH (DENGAN PESAN GLOBAL & SMART TEMPLATE {nama})
     */
    public function resendBatch(Request $request, $batchId)
    {
        $request->validate([
            'new_due_date' => 'nullable|date',
            'global_message' => 'nullable|string', 
        ]);

        $batch = MessageBatch::where('user_id', Auth::id())->findOrFail($batchId);
        $messages = $batch->messages; 

        if ($messages->isEmpty()) return back()->with('error', 'Folder ini kosong.');

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

        foreach ($messages as $msg) {
            
            // LOGIKA SMART REPLACE
            if (!empty($globalMessage)) {
                $finalContent = $globalMessage;
                
                if (str_contains(strtolower($finalContent), '{nama}')) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $msg->phone);
                    
                    $contact = Contact::where('user_id', Auth::id())
                        ->where(function($query) use ($cleanPhone) {
                            $query->where('phone', $cleanPhone)
                                  ->orWhere('phone', '+' . $cleanPhone)
                                  ->orWhere('phone', 'like', '%' . substr($cleanPhone, 2));
                        })
                        ->first();

                    $name = $contact ? $contact->name : ''; 
                    $finalContent = str_ireplace('{nama}', $name, $finalContent);
                }
                $msg->content = $finalContent;
                $msg->save();
            }

            if ($newDueDate) {
                $msg->update(['due_date' => $newDueDate]);
            }

            try {
                $response = Http::withBasicAuth($username, $password)
                    ->timeout(10)
                    ->post($url, [
                        'phoneNumbers' => [$msg->phone], 
                        'message' => $msg->content 
                    ]);

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
     * 6. UPDATE PESAN
     */
    public function updateMessage(Request $request, $id)
    {
        $msg = Message::where('user_id', Auth::id())->findOrFail($id);
        $msg->update([
            'content' => $request->message,
            'due_date' => $request->due_date
        ]);
        return back()->with('success', 'Pesan berhasil diperbarui.');
    }

    /**
     * 7. HAPUS PESAN
     */
    public function deleteMessage($id)
    {
        $msg = Message::where('user_id', Auth::id())->findOrFail($id);
        $msg->delete();
        return back()->with('success', 'Pesan dihapus.');
    }

    /**
     * 8. HALAMAN RIWAYAT PESAN
     */
    public function history() {
        $userId = Auth::id();
        $messages = Message::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(10); 
        $stats = Message::where('user_id', $userId)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        $chartData = ['sent' => $stats['sent'] ?? 0, 'pending' => $stats['pending'] ?? 0, 'failed' => $stats['failed'] ?? 0];
        return view('riwayat', compact('messages', 'chartData'));
    }

    /**
     * 9. HALAMAN JADWAL & PENGINGAT
     */
    public function scheduled()
    {
        $userId = Auth::id();

        $batches = MessageBatch::where('user_id', $userId)
            ->withCount(['messages as total_msg', 'messages as pending_msg' => function($q){
                $q->whereIn('status', ['pending', 'failed']);
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
    
    public function upload(Request $request) { return back(); }
}