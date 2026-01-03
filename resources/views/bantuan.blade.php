@extends('layouts.bar')

@section('title', 'Pusat Bantuan - KIRIMPESAN')
@section('breadcrumb', 'Bantuan')

@section('content')
<div class="container-fluid">
    
    <!-- Header Sambutan -->
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Pusat Bantuan & Panduan 💡</h2>
        <p class="text-muted lead">
            Hai! Bingung cara mulainya? Tenang, kami sudah siapkan panduan lengkap untuk Anda. <br>
            Yuk, kita pelajari cara menghubungkan HP dan mengirim pesan pertamamu!
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- SECTION 1: WAJIB DIBACA (SMS GATEWAY) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-rocket me-2"></i> Langkah Pertama: Hubungkan HP Android Anda</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="fw-bold text-success">Apa itu Android SMS Gateway? 🤔</h5>
                            <p>
                                Aplikasi web ini bekerja seperti "Remote Control". Ia tidak bisa mengirim SMS sendiri, tapi ia memerintahkan HP Android Anda untuk mengirimkannya. 
                                Jadi, kita perlu menginstal aplikasi penghubung di HP Anda.
                            </p>
                            <p>
                                Kami menggunakan teknologi canggih dari <strong>Android SMS Gateway (by Capcom6)</strong>.
                            </p>
                            
                            <div class="alert alert-light border-start border-success border-4 mt-3">
                                <strong>Cara Menghubungkannya:</strong>
                                <ol class="mb-0 mt-2">
                                    <li class="mb-2">Download aplikasi Android-nya melalui link di bawah ini.</li>
                                    <li class="mb-2">Install dan buka aplikasi di HP Anda.</li>
                                    <li class="mb-2">Masuk ke menu <strong>Settings (Pengaturan)</strong> di aplikasi HP.</li>
                                    <li class="mb-2">
                                        Masukkan data server sesuai yang ada di halaman <strong><a href="{{ route('beranda') }}" class="text-success fw-bold">Beranda</a></strong> aplikasi ini:
                                        <ul>
                                            <li><strong>Server URL:</strong> Masukkan <em>Local Address</em> Anda.</li>
                                            <li><strong>Username & Password:</strong> Sesuaikan dengan yang Anda buat.</li>
                                        </ul>
                                    </li>
                                    <li>Aktifkan servisnya, dan selesai! HP Anda siap menembakkan pesan! 🚀</li>
                                    <li>Note : Untuk mencegah error, jangan keluar dari aplikasi SMSGatenya</li>
                                </ol>
                            </div>

                            <div class="mt-4">
                                <a href="https://github.com/capcom6/android-sms-gateway/releases" target="_blank" class="btn btn-outline-success btn-lg me-2">
                                    <i class="fab fa-android me-2"></i> Download APK (GitHub)
                                </a>
                                <a href="https://github.com/capcom6/android-sms-gateway" target="_blank" class="btn btn-link text-muted">
                                    <i class="fab fa-github me-1"></i> Lihat Source Code
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="fas fa-mobile-alt text-success" style="font-size: 10rem; opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: FAQ (ACCORDION) -->
            <h4 class="fw-bold mb-3 mt-5"><i class="fas fa-question-circle me-2 text-success"></i> Pertanyaan Umum</h4>
            
            <div class="accordion shadow-sm" id="accordionHelp">
                
                <!-- Item 1 -->
                <div class="accordion-item border-0 mb-2 rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            📱 Bagaimana cara mengirim pesan masal?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionHelp">
                        <div class="accordion-body bg-light text-muted">
                            Caranya gampang banget! Pergi ke menu <strong>Tulis Pesan</strong>. Di sana Anda punya dua pilihan:
                            <ul>
                                <li><strong>Manual:</strong> Ketik nomor tujuan (bisa banyak sekaligus, pisahkan dengan koma) dan ketik pesan Anda.</li>
                                <li><strong>Excel:</strong> Upload file Excel berisi data kontak. Pastikan file Excel Anda punya kolom 'nomor' dan 'nama' ya!</li>
                            </ul>
                            Klik tombol kirim, dan biarkan aplikasi bekerja untuk Anda.
                        </div>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="accordion-item border-0 mb-2 rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            📄 Format Excel seperti apa yang harus saya gunakan?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionHelp">
                        <div class="accordion-body bg-light text-muted">
                            Agar terbaca dengan sempurna, gunakan format <strong>.xlsx</strong> atau <strong>.xls</strong>. 
                            <br><br>
                            Pastikan baris pertama adalah judul kolom (Header). Misalnya:
                            <br>
                            <code>Kolom A: Nama</code> | <code>Kolom B: Nomor</code>
                            <br><br>
                            Nanti saat menulis pesan, Anda bisa memanggil nama mereka secara otomatis dengan mengetik <code>{nama}</code>. Keren kan? Pesan jadi terasa lebih personal!
                        </div>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="accordion-item border-0 mb-2 rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            ⚠️ Kenapa pesan saya gagal terkirim?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionHelp">
                        <div class="accordion-body bg-light text-muted">
                            Jangan panik! Biasanya ini disebabkan hal-hal sepele:
                            <ol>
                                <li><strong>Koneksi Putus:</strong> Pastikan HP Android Anda menyala, terhubung internet, dan aplikasi Gateway-nya sedang berjalan.</li>
                                <li><strong>Pulsa Habis:</strong> Cek sisa pulsa atau kuota SMS di HP Anda.</li>
                                <li><strong>Konfigurasi Salah:</strong> Cek kembali menu Beranda, apakah IP Address atau Password berubah?</li>
                            </ol>
                            Jika masih bermasalah, coba restart aplikasi di HP Anda.
                        </div>
                    </div>
                </div>

                 <!-- Item 4 -->
                 <div class="accordion-item border-0 mb-2 rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            🔒 Apakah data saya aman?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionHelp">
                        <div class="accordion-body bg-light text-muted">
                            Tentu saja! Aplikasi ini berjalan di jaringan lokal (Localhost) Anda sendiri. Data Excel dan nomor telepon tidak dikirim ke server kami, melainkan langsung diproses di komputer dan HP Anda sendiri. Privasi Anda adalah prioritas kami.
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Note -->
            <div class="text-center mt-5 mb-5">
                <p class="text-muted">Masih butuh bantuan lebih lanjut?</p>
                <a href="https://wa.me/6281234567890" class="btn btn-success btn-sm rounded-pill px-4">
                    <i class="fab fa-whatsapp me-1"></i> Chat Tim Support
                </a>
            </div>

        </div>
    </div>
</div>

{{-- Styling tambahan khusus halaman ini --}}
<style>
    .accordion-button:not(.collapsed) {
        color: #198754;
        background-color: #e8f5e9;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
    }
    .accordion-button:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
</style>
@endsection