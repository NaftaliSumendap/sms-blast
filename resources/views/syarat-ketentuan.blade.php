@extends('layouts.bar')

@section('title', 'Syarat & Ketentuan - KIRIMPESAN')
@section('breadcrumb', 'Syarat & Ketentuan')

@section('content')
<div class="container-fluid">
    
    <!-- Header Ramah -->
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Mari Sepakati Bersama 🤝</h2>
        <p class="text-muted lead">
            Agar penggunaan aplikasi ini nyaman untuk semua, yuk luangkan waktu sejenak <br>
            untuk membaca hal-hal penting di bawah ini. Santai saja, tidak panjang kok!
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Dekorasi Header Card -->
                <div class="card-header bg-white border-bottom-0 pt-5 px-5 pb-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success me-3">
                            <i class="fas fa-file-contract fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Ketentuan Layanan</h4>
                            <small class="text-muted">Terakhir diperbarui: {{ date('d F Y') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    
                    <!-- Poin 1: Pendahuluan -->
                    <div class="mb-5">
                        <h5 class="fw-bold text-success mb-3"><i class="fas fa-smile me-2"></i> 1. Halo & Selamat Datang!</h5>
                        <p class="text-secondary">
                            Terima kasih telah menggunakan <strong>KIRIMPESAN</strong>. Aplikasi ini dibuat untuk membantu Anda menyebarkan informasi dengan cepat dan mudah. Dengan menggunakan aplikasi ini, kami menganggap Anda sudah setuju untuk berteman baik dengan aturan-aturan di sini ya.
                        </p>
                    </div>

                    <!-- Poin 2: Etika Penggunaan -->
                    <div class="mb-5">
                        <h5 class="fw-bold text-success mb-3"><i class="fas fa-heart me-2"></i> 2. Gunakan dengan Bijak</h5>
                        <p class="text-secondary">
                            Kami percaya Anda adalah pengguna yang bertanggung jawab. Namun, sekadar mengingatkan, mohon <strong>JANGAN</strong> gunakan aplikasi ini untuk:
                        </p>
                        <ul class="list-group list-group-flush rounded-3 border-0 bg-light">
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-times-circle text-danger me-2"></i> Mengirim pesan penipuan, spam, atau "mama minta pulsa".</li>
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-times-circle text-danger me-2"></i> Menyebarkan berita bohong (hoax) atau ujaran kebencian.</li>
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-times-circle text-danger me-2"></i> Mempromosikan hal-hal ilegal seperti judi online atau narkoba.</li>
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-times-circle text-danger me-2"></i> Mengganggu privasi orang lain yang tidak ingin dihubungi.</li>
                        </ul>
                    </div>

                    <!-- Poin 3: Tanggung Jawab -->
                    <div class="mb-5">
                        <h5 class="fw-bold text-success mb-3"><i class="fas fa-shield-alt me-2"></i> 3. Tanggung Jawab Pengguna</h5>
                        <p class="text-secondary">
                            Karena aplikasi ini berjalan di perangkat (server lokal) dan menggunakan nomor HP Anda sendiri sebagai pengirim, maka:
                        </p>
                        <div class="alert alert-warning border-0 d-flex align-items-start rounded-3">
                            <i class="fas fa-exclamation-circle mt-1 me-3 fs-5"></i>
                            <div>
                                <strong>Isi pesan sepenuhnya tanggung jawab Anda.</strong> <br>
                                Kami (pembuat aplikasi) tidak bertanggung jawab atas segala dampak hukum atau sosial yang timbul akibat pesan yang Anda kirimkan. Jadi, kirimlah pesan yang membawa manfaat ya!
                            </div>
                        </div>
                    </div>

                    <!-- Poin 4: Privasi Data -->
                    <div class="mb-5">
                        <h5 class="fw-bold text-success mb-3"><i class="fas fa-user-lock me-2"></i> 4. Data Anda Aman</h5>
                        <p class="text-secondary">
                            Kami menghargai privasi Anda. Kabar baiknya:
                        </p>
                        <p class="text-secondary">
                            Semua data kontak, riwayat pesan, dan file Excel yang Anda upload <strong>HANYA tersimpan di komputer/server lokal Anda</strong>. Kami tidak mengunggahnya ke <em>cloud</em> atau server pihak ketiga manapun. Data Anda adalah milik Anda sepenuhnya.
                        </p>
                    </div>

                    <!-- Penutup -->
                    <div class="text-center mt-5 pt-4 border-top">
                        <p class="mb-3 fw-bold text-dark">Setuju dengan ketentuan di atas?</p>
                        <a href="{{ route('tulis-pesan') }}" class="btn btn-success btn-lg px-5 rounded-pill shadow-sm">
                            <i class="fas fa-check me-2"></i> Ya, Saya Setuju & Lanjut
                        </a>
                        <p class="mt-3 small text-muted">
                            Jika ada pertanyaan, silakan mampir ke menu <a href="{{ route('bantuan') }}" class="text-decoration-none text-success">Bantuan</a>.
                        </p>
                    </div>

                </div>
            </div>
            
            <!-- Copyright Kecil -->
            <div class="text-center mt-4 mb-5 text-muted small">
                &copy; {{ date('Y') }} KIRIMPESAN Team. Dibuat dengan ❤️ untuk produktivitas Anda.
            </div>

        </div>
    </div>
</div>
@endsection