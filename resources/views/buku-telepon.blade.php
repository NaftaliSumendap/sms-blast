@extends('layouts.bar')

@section('title', 'Buku Telepon - KIRIMPESAN')
@section('breadcrumb', 'Buku Telepon')

@section('content')
<style>
    /* Custom Nav Pill Style */
    .nav-pills-custom .nav-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-radius: 50rem;
        padding: 8px 20px;
        font-weight: 500;
        margin-right: 10px;
        border: 1px solid #e9ecef;
        transition: all 0.2s;
    }
    .nav-pills-custom .nav-link:hover {
        background-color: #e9ecef;
    }
    .nav-pills-custom .nav-link.active {
        background-color: #198754;
        color: #fff;
        border-color: #198754;
        box-shadow: 0 4px 6px rgba(25, 135, 84, 0.2);
    }
    
    /* Avatar Circle */
    .avatar-circle {
        width: 40px; height: 40px;
        background-color: #e8f5e9;
        color: #198754;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 1.1rem;
    }
    
    /* Table Row Hover Effect */
    .table-hover tbody tr:hover {
        background-color: #fcfcfc;
    }
</style>

<div class="container-fluid">

    {{-- Notifikasi Sukses / Error --}}
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 rounded-4 mb-4" role="alert">
        <i class="fas fa-check-circle fs-4 me-3"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 rounded-4 mb-4" role="alert">
        <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- KOLOM KIRI: FORM INPUT (Sticky di desktop agar tetap terlihat saat scroll tabel) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 position-sticky" style="top: 20px; z-index: 10;">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-user-plus text-success me-2"></i> Tambah Kontak</h5>
                </div>
                <div class="card-body pt-0">
                    
                    <!-- Toggle Manual / Excel -->
                    <ul class="nav nav-pills nav-pills-custom mb-4 justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button">Manual</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-import-tab" data-bs-toggle="pill" data-bs-target="#pills-import" type="button">Import Excel</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        
                        <!-- FORM 1: INPUT MANUAL -->
                        <div class="tab-pane fade show active" id="pills-manual">
                            <form action="{{ route('contacts.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Nama Lengkap</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0 bg-light" placeholder="Contoh: Budi Santoso" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Nomor WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" name="phone" class="form-control border-start-0 bg-light" placeholder="Contoh: 0812345678" required>
                                    </div>
                                    <div class="form-text x-small mt-1 ms-1">Nomor akan otomatis diformat ke 628xxx.</div>
                                </div>
                                <!-- INPUT BARANG JAMINAN -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary small">Barang Jaminan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-gem text-muted"></i></span>
                                        <input type="text" name="collateral" class="form-control border-start-0 bg-light" placeholder="Contoh: Emas 5g, Laptop Asus">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                    <i class="fas fa-save me-1"></i> Simpan Kontak
                                </button>
                            </form>
                        </div>

                        <!-- FORM 2: IMPORT EXCEL -->
                        <div class="tab-pane fade" id="pills-import">
                            <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="alert alert-info small border-0 bg-info bg-opacity-10 text-info mb-3 rounded-3">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle me-2 mt-1"></i>
                                        <div>
                                            <strong>Format Excel Wajib:</strong><br>
                                            • Kolom A: Nama Kontak<br>
                                            • Kolom B: Nomor HP<br>
                                            • Kolom C: Barang Jaminan (Opsional)<br>
                                            <span class="fst-italic text-muted" style="font-size: 0.8em;">(Baris pertama dianggap Header & dilewati)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary small">Pilih File Excel</label>
                                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                    <i class="fas fa-file-import me-1"></i> Import Data
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: DAFTAR KONTAK -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg border-0 rounded-4 h-100">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom-0">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="fas fa-address-book text-success me-2"></i> Daftar Kontak</h5>
                        <p class="text-muted small mb-0">Total Tersimpan: <strong>{{ $contacts->total() }}</strong> Orang</p>
                    </div>
                    
                    <!-- Tombol Hapus Semua (Bahaya) -->
                    @if($contacts->count() > 0)
                    <form action="{{ route('contacts.destroyAll') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus SEMUA kontak di buku telepon? Tindakan ini tidak bisa dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 border-opacity-25">
                            <i class="fas fa-trash-alt me-1"></i> Hapus Semua
                        </button>
                    </form>
                    @endif
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="px-4 py-3 border-0">Nama</th>
                                    <th class="px-4 py-3 border-0">Nomor HP</th>
                                    <th class="px-4 py-3 border-0">Barang Jaminan</th>
                                    <th class="px-4 py-3 border-0 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $contact)
                                <tr>
                                    <td class="px-4 text-dark">
                                        <div class="d-flex align-items-center">
                                            <!-- Avatar Inisial -->
                                            <div class="avatar-circle me-3 shadow-sm text-uppercase">
                                                {{ substr($contact->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="fw-bold d-block">{{ $contact->name }}</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">Ditambahkan {{ $contact->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4">
                                        <span class="badge bg-light text-dark border fw-normal font-monospace px-2 py-1">
                                            {{ $contact->phone }}
                                        </span>
                                    </td>

                                    <!-- DATA BARANG JAMINAN -->
                                    <td class="px-4">
                                        @if($contact->collateral)
                                            <span class="badge bg-warning text-dark border border-warning-subtle"><i class="fas fa-gem me-1"></i> {{ $contact->collateral }}</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 text-end">
                                        <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kontak {{ $contact->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-sm text-danger border rounded-circle shadow-sm" title="Hapus Kontak" style="width: 32px; height: 32px;">
                                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                            <div class="bg-light rounded-circle p-4 mb-3">
                                                <i class="fas fa-user-friends fa-3x text-secondary opacity-25"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark">Buku Telepon Kosong</h6>
                                            <p class="text-muted small mb-0">Mulai tambahkan kontak secara manual atau import dari Excel.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end">
                    {{ $contacts->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection