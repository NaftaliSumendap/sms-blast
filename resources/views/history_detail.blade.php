<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Riwayat SMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-700 flex items-center gap-2">
            📩 Detail Riwayat SMS
        </h1>
        <a href="{{ url('/history') }}" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Riwayat</a>

        <!-- Info Ringkas -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <p><b>Tanggal:</b> {{ $history->created_at }}</p>
                <p><b>Tipe:</b> {{ ucfirst($history->type) }}</p>
                <p><b>File:</b> {{ $history->file_name ?? '-' }}</p>
            </div>
            <div>
                <p><b>Total:</b> {{ $history->total }}</p>
                <p><b>Berhasil:</b> <span class="text-green-600">{{ $history->success }}</span></p>
                <p><b>Gagal:</b> <span class="text-red-600">{{ $history->failed }}</span></p>
            </div>
        </div>

        <!-- Tabel Detail Pesan -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 rounded">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="px-3 py-2 border">#</th>
                        <th class="px-3 py-2 border">Nomor Tujuan</th>
                        <th class="px-3 py-2 border">Pesan</th>
                        <th class="px-3 py-2 border">Status</th>
                        <th class="px-3 py-2 border">Error</th>
                        <th class="px-3 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $i => $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border text-center">{{ $i+1 }}</td>
                            <td class="px-3 py-2 border">{{ $d['number'] ?? '-' }}</td>
                            <td class="px-3 py-2 border text-xs">{{ $d['message'] ?? '-' }}</td>
                            <td class="px-3 py-2 border text-center">
                                @if(($d['status'] ?? '') === 'success')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded">✅ Berhasil</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded">❌ Gagal</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border text-xs text-red-600">{{ $d['error'] ?? '-' }}</td>
                            <td class="px-3 py-2 border text-center">
                                @if(($d['status'] ?? '') === 'failed')
                                    <form action="{{ url('/sms/retry-single/'.$history->id.'/'.$i) }}" method="POST">
                                        @csrf
                                        <button class="text-blue-600 underline">Kirim Ulang</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-2 border text-center text-gray-500">Tidak ada data detail</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
