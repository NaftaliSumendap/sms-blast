<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Riwayat SMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-700 text-center flex items-center justify-center gap-2">
            📜 Riwayat Pengiriman SMS
        </h1>
        <a href="{{ url('/') }}" class="text-blue-600 underline mb-4 inline-block">← Kembali ke SMS Blast</a>

        <!-- Grafik Statistik -->
        <canvas id="smsChart" class="my-6"></canvas>
        <script>
            const ctx = document.getElementById('smsChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_keys($chartData->toArray())) !!},
                    datasets: [
                        {
                            label: 'Berhasil',
                            data: {!! json_encode($chartData->pluck('success')->values()) !!},
                            borderColor: 'green',
                            backgroundColor: 'rgba(34,197,94,0.2)',
                            fill: true,
                        },
                        {
                            label: 'Gagal',
                            data: {!! json_encode($chartData->pluck('failed')->values()) !!},
                            borderColor: 'red',
                            backgroundColor: 'rgba(239,68,68,0.2)',
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>

        <!-- Tabel Riwayat -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 rounded">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="px-3 py-2 border">Waktu</th>
                        <th class="px-3 py-2 border">Tipe</th>
                        <th class="px-3 py-2 border">File</th>
                        <th class="px-3 py-2 border">Template</th>
                        <th class="px-3 py-2 border">Total</th>
                        <th class="px-3 py-2 border">Berhasil</th>
                        <th class="px-3 py-2 border">Gagal</th>
                        <th class="px-3 py-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $h)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $h->created_at }}</td>
                            <td class="px-3 py-2 border">
                                @if($h->type === 'upload')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded">Upload</span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Manual</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 border">{{ $h->file_name ?? '-' }}</td>
                            <td class="px-3 py-2 border text-xs">{{ Str::limit($h->template, 20) }}</td>
                            <td class="px-3 py-2 border text-center">{{ $h->total }}</td>
                            <td class="px-3 py-2 border text-green-700 text-center">{{ $h->success }}</td>
                            <td class="px-3 py-2 border text-red-700 text-center">{{ $h->failed }}</td>
                            <td class="px-3 py-2 border text-center">
                                <!-- Tombol Detail -->
                                <button onclick="toggleDetail({{ $h->id }})" class="text-blue-600 underline">Lihat</button>

                                <!-- Tombol Retry jika ada gagal -->
                                @if($h->failed > 0)
                                    <form action="{{ url('/sms/retry/'.$h->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="text-red-600 underline ml-2">Kirim Ulang Gagal</button>
                                    </form>
                                @endif

                                <!-- Detail -->
                                <div id="detail-{{ $h->id }}" class="hidden mt-2">
                                    <pre class="bg-gray-50 p-2 rounded text-xs text-left">{{ json_encode(json_decode($h->details), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleDetail(id) {
            let el = document.getElementById('detail-' + id);
            el.classList.toggle('hidden');
        }
    </script>
</body>
</html>
