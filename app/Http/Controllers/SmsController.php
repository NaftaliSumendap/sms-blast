<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use App\Models\SmsHistory;

class SmsController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'phone'   => ['required', 'regex:/^(\+?\d{10,15})(,\+?\d{10,15})*$/'],
            'message' => 'required|string',
        ]);

        // Pisahkan nomor
        $phones = array_map('trim', explode(',', $request->phone));
        $phones = array_filter($phones, fn($p) => !empty($p));

        // Tambahkan + kalau belum ada (E.164 format)
        $phones = array_map(fn($p) => preg_match('/^\+/', $p) ? $p : ('+' . $p), $phones);

        $url      = 'http://192.168.18.181:8080/message';   // pakai /message
        $username = env('SMS_GATE_USERNAME', 'sms');        // sesuaikan dengan app
        $password = env('SMS_GATE_PASSWORD', 'vBQHwwtQ');   // sesuaikan dengan app
        $deviceId = env('SMS_GATE_DEVICE_ID', 'ffffffff9496f2ca000001995c4f3800');

        $payload = [
            'phoneNumbers' => $phones,
            'message'      => $request->message,
            'deviceId'     => $deviceId,
        ];

        $response = Http::withBasicAuth($username, $password)
            ->withHeaders(['Accept' => 'application/json'])
            ->post($url, $payload);

        if ($response->successful()) {
            $successCount = count($phones);
            $failCount    = 0;
            $statusMsg    = 'Berhasil';
        } else {
            $successCount = 0;
            $failCount    = count($phones);
            $statusMsg    = 'Gagal';
        }

        SmsHistory::create([
            'type'      => 'manual',
            'total'     => count($phones),
            'success'   => $successCount,
            'failed'    => $failCount,
            'details'   => json_encode([[
                'nomor'  => implode(',', $phones),
                'pesan'  => $request->message,
                'status' => $statusMsg,
                'error'  => $response->successful() ? null : ($response->json('message') ?? $response->body()),
            ]]),
        ]);

        return back()->with(
            $response->successful() ? 'success' : 'error',
            $response->successful() ? 'SMS berhasil dikirim!' : "Gagal ({$response->status()}): " . $response->body()
        );
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|mimes:xlsx,xls',
            'template' => 'required|string',
        ]);

        $rows     = Excel::toArray([], $request->file('file'))[0];
        $template = $request->input('template');

        // Konfigurasi Local Server
        $url      = 'http://192.168.18.181:8080/message';
        $username = env('SMS_GATE_USERNAME', 'sms');
        $password = env('SMS_GATE_PASSWORD', 'vBQHwwtQ');
        $deviceId = env('SMS_GATE_DEVICE_ID', 'ffffffff9496f2ca000001995c4f3800');

        $success = 0; 
        $failed  = 0; 
        $errors  = [];

        // Ambil header dari baris pertama Excel
        $headers = array_map(fn($h) => strtolower(trim($h)), $rows[0]);

        $debugRows = [];

        foreach (array_slice($rows, 1) as $row) {
            
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), null);
            }

            $data = array_combine($headers, $row);

            // Konversi jika kolom tanggal berupa angka Excel
            if (isset($data['tanggal'])) {
                $tanggal = $data['tanggal'];

                if (is_numeric($tanggal)) {
                    // Convert serial number Excel → Carbon date
                    $data['tanggal'] = Carbon::instance(Date::excelToDateTimeObject($tanggal))
                                            ->format('d/m/Y');
                } else {
                    // Kalau sudah string, biarin
                    $data['tanggal'] = $tanggal;
                }
            }

            // === Lanjut generate pesan ===
            $phone = trim($data['nomor'] ?? '');
            if (!$phone) continue;

            $message = $template;
            foreach ($data as $key => $value) {
                $message = str_replace('{'.$key.'}', $value ?? '', $message);
            }
            $message = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $message);

            $payload = [
                'phoneNumbers' => [ $phone ],
                'message'      => $message,
                'deviceId'     => $deviceId,
            ];

            $response = Http::withBasicAuth($username, $password)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($url, $payload);

            $status = $response->successful() ? 'Berhasil' : 'Gagal';
            $errorMsg = $response->successful() ? null : ($response->json('message') ?? $response->body());

            $debugRows[] = [
                'nama'    => $data['nama'] ?? '',
                'nomor'   => $phone,
                'pesan'   => $message,
                'status'  => $status,
                'error'   => $errorMsg,
            ];

            if ($response->successful()) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Nomor: $phone, Error: " . $errorMsg;
            }
        }

        $msg = "$success SMS berhasil dikirim, $failed gagal.";
        if ($failed) {
            $msg .= ' Detail error: ' . implode('; ', $errors);
        }

        // Kirim debugRows ke session
    SmsHistory::create([
        'type'      => 'upload',
        'file_name' => $request->file('file')->getClientOriginalName(),
        'template'  => $request->template,
        'total'     => count($debugRows),
        'success'   => $success,
        'failed'    => $failed,
        'details'   => json_encode($debugRows),
    ]);

    return back()
        ->with($failed ? 'error' : 'success', $msg)
        ->with('debugRows', $debugRows);

    
    $msg = "$success SMS berhasil dikirim, $failed gagal.";
    if ($failed) {
        $msg .= ' Detail error: ' . implode('; ', $errors);
    }

        // Baru return ke halaman
    return back()
        ->with($failed ? 'error' : 'success', $msg)
        ->with('debugRows', $debugRows);
    }

    public function progressStatus($id)
    {
        // Ambil data progress dari cache/database
        $progress = cache("sms_progress_$id");
        return response()->json($progress ?? ['rows' => [], 'done' => true]);
    }

    public function history()
    {
        $histories = \App\Models\SmsHistory::orderBy('created_at', 'desc')->get();

        // Buat data statistik per hari
        $chartData = $histories->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function ($rows) {
            return [
                'success' => $rows->sum('success'),
                'failed' => $rows->sum('failed'),
            ];
        });

        return view('history', compact('histories', 'chartData'));
    }

}

