@extends('layouts.bar')

@section('title', 'Beranda - Pengaturan Koneksi')
@section('breadcrumb', 'Beranda')

@section('content')
    <!-- Header Halaman -->
    <!-- <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Konfigurasi Awal</h1>
        <small class="text-muted">Silakan masukkan data koneksi server lokal Anda</small>
    </div> -->

    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Menampilkan Error Validasi jika ada input yang salah --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- KARTU UTAMA: FORM PENGATURAN -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-cog me-2 text-success"></i> Pengaturan Koneksi</h5>
                </div>
                <div class="card-body p-4">
                    
                    <form action="{{ route('config.update') }}" method="POST">
                        @csrf
                        
                        <!-- Local Address -->
                        <div class="mb-4">
                            <label for="local_address" class="form-label fw-bold">Local Address</label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('local_address') is-invalid @enderror" 
                                   id="local_address" 
                                   name="local_address" 
                                   placeholder="Contoh: http://127.0.0.1:8000"
                                   value="{{ old('local_address', $config->local_address ?? '') }}" 
                                   required>
                            <small class="form-text text-muted">Masukkan alamat URL server lokal Anda.</small>
                        </div>

                        <!-- Username -->
                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="username" 
                                   name="username" 
                                   placeholder="Masukkan username Anda" 
                                   value="{{ old('username', $config->username ?? '') }}"
                                   required>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Masukkan password Anda" 
                                   value="{{ old('password', $config->password ?? '') }}"
                                   required>
                            <small class="text-muted">*Password ditampilkan dalam bentuk teks agar mudah diperiksa.</small>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- BAGIAN BARU: BANTUAN YANG RAMAH -->
            <div class="card border-0 bg-light shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white p-3 rounded-circle shadow-sm text-success me-4">
                        <i class="fas fa-life-ring fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Masih bingung cara mengisinya? 🤔</h5>
                        <p class="text-muted mb-0">
                            Jangan khawatir! Kami sudah menyiapkan panduan langkah demi langkah untuk membantu Anda menghubungkan aplikasi ini.
                        </p>
                        <a href="{{ route('bantuan') }}" class="btn btn-link text-success fw-bold text-decoration-none p-0 mt-1">
                            Baca Panduan Bantuan <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection