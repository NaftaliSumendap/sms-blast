<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMS Blast</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
      <h1 class="text-2xl font-bold text-white text-center">📲 SMS Blast</h1>
    </div>

    <div class="p-8">
      <!-- Alerts -->
      @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 shadow-sm">
          ✅ {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 shadow-sm">
          ⚠️ {{ session('error') }}
        </div>
      @endif

      <!-- Upload Excel -->
      <form method="POST" action="{{ route('sms.upload') }}" enctype="multipart/form-data" class="mb-10 space-y-4">
          @csrf
          <label class="block font-semibold text-gray-700">Upload File Excel</label>
          <input type="file" name="file" accept=".xlsx,.xls"
                class="file:mr-4 file:py-2 file:px-4 
                        file:rounded-full file:border-0 
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        border border-gray-300 rounded-lg p-2 w-full text-gray-700" required>

          <label class="block font-semibold text-gray-700 mt-4">Template Pesan</label>
          <textarea name="template" rows="3"
                    placeholder="Contoh: Halo {nama}, jangan lupa hadir besok!"
                    class="border border-gray-300 rounded-lg p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required></textarea>

          <button type="submit"
                  class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg shadow-md transition">
            ⬆️ Upload & Kirim SMS
          </button>
      </form>

      <div class="relative flex items-center mb-10">
        <div class="flex-grow border-t border-gray-300"></div>
        <span class="flex-shrink mx-4 text-gray-400 font-medium">atau</span>
        <div class="flex-grow border-t border-gray-300"></div>
      </div>

      <!-- Kirim Manual -->
      <form method="POST" action="{{ route('sms.send') }}" class="space-y-5">
        @csrf
        <div>
          <label class="block font-semibold text-gray-700 mb-1">Nomor Tujuan</label>
          <input type="text" name="phone"
                 placeholder="6281234567890,6289876543210"
                 class="border border-gray-300 rounded-lg p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
          <label class="block font-semibold text-gray-700 mb-1">Pesan</label>
          <textarea name="message" rows="4"
                    class="border border-gray-300 rounded-lg p-3 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
        </div>
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition">
          🚀 Kirim SMS
        </button>
      </form>
      <div class="text-center mt-6">
        <a href="{{ route('sms.history') }}" class="text-indigo-600 hover:underline font-medium">📜 Lihat Riwayat Pengiriman</a>
    </div>
  </div>



</body>
<script>
let processId = null; // Dapatkan dari response upload
function pollProgress() {
    if (!processId) return;
    fetch('/progress-status/' + processId)
        .then(res => res.json())
        .then(data => {
            // Update progress bar/tabel di halaman
            // data.rows = array hasil progress
            // data.done = true jika sudah selesai
            updateProgressTable(data.rows);
            if (!data.done) setTimeout(pollProgress, 2000);
        });
}
function updateProgressTable(rows) {
    // Render tabel progress sesuai data.rows
}
</script>
</html>
