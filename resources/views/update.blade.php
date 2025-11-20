@extends('layouts.bar')

{{-- Mendefinisikan judul halaman --}}
@section('title', 'Update Aplikasi - KIRIMPESAN')

{{-- Mendefinisikan breadcrumb --}}
@section('breadcrumb', 'Update Aplikasi')

{{-- Mendefinisikan konten utama --}}
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-sync-alt me-2 text-success"></i> Informasi Versi Aplikasi</h5>
            </div>
            <div class="card-body p-4 p-md-5 text-center">
                
                {{-- Pesan Versi Terbaru --}}
                <div class="alert alert-success d-flex align-items-center justify-content-center" role="alert">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-0">Hore! Anda sudah menggunakan versi terbaru.</h4>
                    </div>
                </div>

                <p class="lead mt-4">
                    Terima kasih telah menggunakan aplikasi ini. Kami selalu bekerja keras untuk memberikan fitur dan keamanan terbaik untuk semua pegawai Pegadaian.
                </p>
                
                <hr class="my-4">

                <h5 class="fw-bold">Punya Ide atau Masukan?</h5>
                <p class="text-muted">
                    Kami sangat senang mendengar saran dan kritik dari Anda untuk membuat aplikasi ini menjadi lebih baik lagi.
                    Jangan ragu untuk menghubungi kami melalui WhatsApp di:
                </p>
                
                {{-- GANTI NOMOR DI BAWAH INI --}}
                <a href="https://wa.me/6281234567890" class="btn btn-success btn-lg shadow-sm" target="_blank">
                    <i class="fab fa-whatsapp me-2"></i> Hubungi Kami (0812-3456-7890)
                </a>

            </div>
        </div>
    </div>
</div>
@endsection