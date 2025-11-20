<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConnectionSetting;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    // Menampilkan halaman beranda beserta data pengaturan yang tersimpan
    public function index()
    {
        // Ambil pengaturan milik user yang sedang login
        $config = ConnectionSetting::where('user_id', Auth::id())->first();

        return view('beranda', compact('config'));
    }

    // Menyimpan atau Mengupdate Pengaturan
    public function update(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'local_address' => 'required|url', // Pastikan format URL
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        // 2. Simpan atau Update (Update jika ada, Buat baru jika tidak ada)
        ConnectionSetting::updateOrCreate(
            ['user_id' => Auth::id()], // Kunci pencarian (berdasarkan user login)
            [
                'local_address' => $validated['local_address'],
                'username' => $validated['username'],
                'password' => $validated['password'],
            ]
        );

        // 3. Kembali dengan pesan sukses (Pop-up Toast Anda akan muncul otomatis karena logic di layout)
        return back()->with('status', 'Pengaturan koneksi berhasil diperbarui! 🚀');
    }
}