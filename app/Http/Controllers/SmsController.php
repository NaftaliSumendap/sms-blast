<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

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
            return back()->with('success', 'SMS berhasil dikirim lewat Local Server!');
        } else {
            return back()->with('error', "Gagal ({$response->status()}): " . $response->body());
        }
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $rows = Excel::toArray([], $request->file('file'))[0];

        // Konfigurasi Local Server
        $url      = 'http://192.168.18.181:8080/message'; // IP HP kamu + path /message
        $username = env('SMS_GATE_USERNAME', 'sms');      // sesuaikan dgn app
        $password = env('SMS_GATE_PASSWORD', 'vBQHwwtQ'); // sesuaikan dgn app
        $deviceId = env('SMS_GATE_DEVICE_ID', 'ffffffff9496f2ca000001995c4f3800');

        $success = 0; 
        $failed  = 0; 
        $errors  = [];

        foreach (array_slice($rows, 1) as $row) {
            $name    = $row[0] ?? '';
            $phone   = trim($row[1] ?? '');
            $message = $row[2] ?? '';

            if (!$phone || !$message) {
                $failed++; 
                continue;
            }

            // pastikan nomor format internasional
            if (!preg_match('/^\+/', $phone)) {
                $phone = '+'.$phone;
            }

            $payload = [
                'phoneNumbers' => [ $phone ],
                'message'      => $message,
                'deviceId'     => $deviceId,
            ];

            $response = Http::withBasicAuth($username, $password)
                ->withHeaders(['Accept' => 'application/json'])
                ->post($url, $payload);

            if ($response->successful()) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Nomor: $phone, Error: " 
                          . ($response->json('message') ?? $response->body());
            }
        }

        $msg = "$success SMS berhasil dikirim, $failed gagal.";
        if ($failed) {
            $msg .= ' Detail error: ' . implode('; ', $errors);
        }

        return back()->with($failed ? 'error' : 'success', $msg);
    }
}

