<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    /**
     * Menampilkan halaman buku telepon.
     */
    public function index()
    {
        $contacts = Contact::where('user_id', Auth::id())
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        return view('buku-telepon', compact('contacts'));
    }

    /**
     * Menyimpan kontak baru (Manual).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'collateral' => 'nullable|string|max:255', // Validasi barang jaminan
        ]);

        // Format nomor (Hapus karakter non-angka)
        $phone = preg_replace('/[^0-9]/', '', $request->phone);
        // Ubah awalan 08 menjadi 628
        if (str_starts_with($phone, '08')) {
            $phone = '62' . substr($phone, 2);
        }

        // Simpan atau update
        Contact::updateOrCreate(
            ['user_id' => Auth::id(), 'phone' => $phone],
            [
                'name' => $request->name,
                'collateral' => $request->collateral // Simpan data jaminan
            ]
        );

        return back()->with('success', 'Kontak berhasil disimpan!');
    }

    /**
     * Import kontak dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $dataArray = Excel::toArray([], $request->file('file'))[0];

        // Asumsi Header: Baris 1
        // Kolom A (0) = Nama
        // Kolom B (1) = Nomor HP
        // Kolom C (2) = Barang Jaminan (Opsional)
        
        $count = 0;
        
        foreach (array_slice($dataArray, 1) as $row) {
            if (!isset($row[1])) continue; // Skip jika tidak ada nomor

            $name = $row[0] ?? 'Tanpa Nama';
            $rawPhone = trim((string)$row[1]);
            $collateral = $row[2] ?? null; // Ambil kolom C sebagai jaminan

            if (empty($rawPhone)) continue;

            // Format Nomor
            $phone = preg_replace('/[^0-9]/', '', $rawPhone);
            if (str_starts_with($phone, '08')) {
                $phone = '62' . substr($phone, 2);
            }

            // Simpan ke database
            Contact::updateOrCreate(
                ['user_id' => Auth::id(), 'phone' => $phone],
                [
                    'name' => $name,
                    'collateral' => $collateral // Update jaminan
                ]
            );
            $count++;
        }

        return back()->with('success', "Berhasil mengimpor $count kontak!");
    }

    /**
     * Menghapus satu kontak.
     */
    public function destroy($id)
    {
        $contact = Contact::where('user_id', Auth::id())->where('id', $id)->first();
        if ($contact) {
            $contact->delete();
            return back()->with('success', 'Kontak berhasil dihapus.');
        }
        return back()->with('error', 'Gagal menghapus.');
    }

    /**
     * Menghapus SEMUA kontak.
     */
    public function destroyAll()
    {
        Contact::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Semua kontak berhasil dibersihkan.');
    }
}