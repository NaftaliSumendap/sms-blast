@extends('layouts.bar')

@section('title', 'Pengiriman Terjadwal - KIRIMPESAN')
@section('breadcrumb', 'Pengiriman Terjadwal')

@section('content')
<style>
    /* --- STYLE FOLDER --- */
    .folder-card {
        background-color: #fff;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .folder-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        border-color: #198754;
    }
    .folder-icon-container {
        width: 60px;
        height: 60px;
        background-color: #fff8e1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    .folder-icon {
        font-size: 1.8rem;
        color: #ffc107; 
    }
    .folder-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.7rem;
        padding: 5px 10px;
    }
    .folder-title {
        font-weight: 700;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 1.1rem;
    }
    .folder-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* --- STYLE TABEL & PREVIEW --- */
    .table-custom thead th { 
        background-color: #f8f9fa; 
        color: #6c757d; 
        font-size: 0.8rem; 
        text-transform: uppercase; 
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }
    .table-custom tbody tr:hover {
        background-color: #fcfcfc;
    }
    .btn-delete-row:hover { color: #dc3545; }

    /* --- STYLE MODAL RESEND --- */
    .resend-preview-list { max-height: 250px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px; }
    .resend-item { padding: 10px 15px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
    .resend-item:last-child { border-bottom: none; }
</style>

<div class="container-fluid">

    {{-- ====================================================================== --}}
    {{-- NOTIFIKASI BERHASIL / GAGAL --}}
    {{-- ====================================================================== --}}
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 rounded-4 mb-4" role="alert">
        <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-check fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-0">Berhasil!</h6>
            <p class="mb-0 small">{{ session('success') }}</p>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 rounded-4 mb-4" role="alert">
        <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="fas fa-exclamation-triangle fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-0">Terjadi Kesalahan</h6>
            <p class="mb-0 small">{{ session('error') }}</p>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    {{-- ====================================================================== --}}
    {{-- MODE 1: PREVIEW EXCEL (Hanya Muncul Setelah Upload) --}}
    {{-- ====================================================================== --}}
    @if(isset($previewData) && count($previewData) > 0)
        
        <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-4 p-4 rounded-4" role="alert">
            <div class="bg-white p-3 rounded-circle text-warning me-4 shadow-sm">
                <i class="fas fa-file-import fs-3"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-1">Konfirmasi Import Excel</h5>
                <p class="mb-0 text-muted">
                    Silakan tinjau data di bawah ini. Hapus baris yang tidak perlu, lalu klik <strong>Kirim & Jadwalkan</strong>.
                </p>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list-check text-primary me-2"></i> Daftar Penerima ({{ count($previewData) }})</h5>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('sms.process_batch') }}" method="POST">
                    @csrf
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead class="sticky-top bg-white" style="z-index: 10;">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Nama</th>
                                    <th>Jaminan</th>
                                    <th>Nomor Tujuan</th>
                                    <th>Isi Pesan</th>
                                    <th>Jadwal</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData as $index => $data)
                                <tr id="row-{{ $index }}">
                                    <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                    
                                    <td>
                                        <span class="fw-bold text-dark">{{ $data['name'] }}</span>
                                        <input type="hidden" name="names[]" value="{{ $data['name'] }}">
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $data['collateral'] ?? '-' }}</span>
                                        <input type="hidden" name="collaterals[]" value="{{ $data['collateral'] }}">
                                    </td>

                                    <td>
                                        <span class="fw-bold text-dark font-monospace">{{ $data['phone'] }}</span>
                                        <input type="hidden" name="phones[]" value="{{ $data['phone'] }}">
                                    </td>

                                    <td>
                                        <div class="text-truncate text-secondary" style="max-width: 250px;" title="{{ $data['message'] }}">
                                            {{ $data['message'] }}
                                        </div>
                                        <input type="hidden" name="messages[]" value="{{ $data['message'] }}">
                                    </td>

                                    <td>
                                        @if($data['due_date'])
                                            <span class="badge bg-warning text-dark border border-warning-subtle">
                                                <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($data['due_date'])->format('d M H:i') }}
                                            </span>
                                            <input type="hidden" name="due_dates[]" value="{{ $data['due_date'] }}">
                                        @else
                                            <span class="text-muted small">-</span>
                                            <input type="hidden" name="due_dates[]" value="">
                                        @endif
                                    </td>

                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-light btn-sm text-danger rounded-circle border shadow-sm btn-delete-row" onclick="document.getElementById('row-{{ $index }}').remove();" title="Hapus Baris">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white py-4 px-4 text-end border-top">
                        <a href="{{ route('tulis-pesan') }}" class="btn btn-light border rounded-pill px-4 fw-bold me-2">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                            <i class="fas fa-paper-plane me-2"></i> Kirim & Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- ====================================================================== --}}
    {{-- MODE 2: TAMPILAN UTAMA (FOLDER & MANUAL) --}}
    {{-- ====================================================================== --}}
    @else
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-1">Jadwal Pengiriman</h3>
            <p class="text-muted mb-0">Kelola pesan terjadwal dari upload Excel maupun manual.</p>
        </div>
        <button class="btn btn-light border rounded-pill px-3 py-2 fw-bold text-secondary shadow-sm" onclick="location.reload()">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
    </div>

    <!-- FOLDER EXCEL -->
    <div class="d-flex align-items-center mb-3">
        <h6 class="fw-bold text-secondary text-uppercase small ls-1 mb-0"><i class="fas fa-folder-open me-2"></i> Batch Upload (Excel)</h6>
        <div class="flex-grow-1 border-bottom ms-3"></div>
    </div>
    
    <div class="row g-4 mb-5">
        @forelse($batches as $batch)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <!-- Kartu Folder -->
            <div class="folder-card p-4 text-center h-100 d-flex flex-column" data-bs-toggle="modal" data-bs-target="#folderModal{{ $batch->id }}">
                <div class="folder-icon-container shadow-sm">
                    <i class="fas fa-folder-open text-warning"></i>
                </div>
                
                @if($batch->pending_msg > 0)
                    <span class="badge bg-danger rounded-pill folder-badge shadow-sm">{{ $batch->pending_msg }} Belum Sukses</span>
                @else
                    <span class="badge bg-success rounded-pill folder-badge shadow-sm"><i class="fas fa-check"></i> Selesai</span>
                @endif

                <div class="folder-title mb-1" title="{{ $batch->batch_name }}">
                    {{ $batch->batch_name }}
                </div>
                <div class="folder-meta mb-3">
                    {{ $batch->created_at->format('d M Y, H:i') }}
                </div>
                
                <div class="mt-auto pt-3 border-top border-light">
                    <span class="badge bg-light text-dark border fw-normal px-3 py-2">
                        <i class="fas fa-envelope me-2 text-secondary"></i> {{ $batch->total_msg }} Pesan
                    </span>
                </div>
            </div>

            <!-- MODAL ISI FOLDER -->
            <div class="modal fade" id="folderModal{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content rounded-4 border-0 shadow-lg">
                        
                        <!-- HEADER MODAL (DI SINI TOMBOL KIRIM ULANG MASSAL) -->
                        <div class="modal-header bg-light border-bottom-0 py-3 px-4">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div>
                                    <h5 class="modal-title fw-bold d-flex align-items-center"><i class="fas fa-folder-open text-warning me-2"></i> {{ $batch->batch_name }}</h5>
                                    <small class="text-muted">Total {{ $batch->total_msg }} pesan.</small>
                                </div>
                                
                                <div class="d-flex align-items-center gap-2">
                                    {{-- TOMBOL KIRIM ULANG BATCH (SELALU MUNCUL) --}}
                                    <button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#resendAllModal{{ $batch->id }}">
                                        <i class="fas fa-paper-plane me-2"></i> Kirim Ulang Batch
                                    </button>
                                    
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-body px-4 pb-4 bg-light">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="list-group list-group-flush rounded-4">
                                    @foreach($batch->messages as $msg)
                                    <div class="list-group-item px-4 py-3 border-bottom">
                                            <div class="row align-items-center g-3">
                                                <div class="col-md-3">
                                                    <div class="d-flex align-items-center mb-1">
                                                        @if($msg->status == 'pending') <span class="badge bg-warning text-dark me-2">Pending</span>
                                                        @elseif($msg->status == 'sent') <span class="badge bg-success me-2">Terkirim</span> 
                                                        @else <span class="badge bg-danger me-2">Gagal</span> @endif
                                                        <span class="font-monospace fw-bold text-dark">{{ $msg->phone }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                    <form action="{{ route('sms.update', $msg->id) }}" method="POST" class="row g-2">
                                                        @csrf @method('PUT')
                                                        <div class="col-4"><input type="datetime-local" name="due_date" class="form-control form-control-sm" value="{{ $msg->due_date ? $msg->due_date->format('Y-m-d\TH:i') : '' }}"></div>
                                                        <div class="col-7"><textarea name="message" class="form-control form-control-sm" rows="1">{{ $msg->content }}</textarea></div>
                                                        <div class="col-1"><button type="submit" class="btn btn-success btn-sm rounded-circle" title="Simpan"><i class="fas fa-save"></i></button></div>
                                                    </form>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group">
                                                        <form action="{{ route('sms.resend', $msg->id) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-light btn-sm border text-primary" title="Kirim Ulang"><i class="fas fa-paper-plane"></i></button></form>
                                                        <a href="{{ route('sms.delete', $msg->id) }}" class="btn btn-light btn-sm border text-danger ms-1" onclick="event.preventDefault(); if(confirm('Hapus?')) document.getElementById('del-form-{{ $msg->id }}').submit();"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <form id="del-form-{{ $msg->id }}" action="{{ route('sms.delete', $msg->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL 2: KONFIRMASI KIRIM ULANG (DENGAN INPUT GLOBAL MESSAGE) -->
            <div class="modal fade" id="resendAllModal{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <form action="{{ route('sms.resend_batch', $batch->id) }}" method="POST" class="w-100">
                        @csrf
                        <div class="modal-content rounded-4 border-0 shadow-lg">
                            
                            <div class="modal-header bg-white border-bottom py-3 px-4 sticky-top">
                                <div>
                                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-edit me-2 text-primary"></i> Edit & Kirim Ulang Massal</h5>
                                    <p class="mb-0 small text-muted">Atur pesan baru untuk seluruh penerima di folder ini.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <div class="modal-body px-4 pb-4 bg-light">
                                
                                <div class="row g-3">
                                    <!-- Input Tanggal Baru -->
                                    <div class="col-md-6">
                                        <div class="bg-white p-3 rounded-3 h-100 border shadow-sm">
                                            <label class="form-label fw-bold small text-dark mb-1">
                                                <i class="far fa-calendar-alt text-primary me-1"></i> Tanggal Jatuh Tempo Baru
                                            </label>
                                            <input type="datetime-local" name="new_due_date" class="form-control form-control-sm">
                                            <div class="form-text x-small text-muted mt-1">Biarkan kosong jika tidak ingin mengubah jadwal.</div>
                                        </div>
                                    </div>

                                    <!-- Input Pesan Baru (GLOBAL UNTUK SEMUA) -->
                                    <div class="col-md-6">
                                        <div class="bg-white p-3 rounded-3 h-100 border shadow-sm">
                                            <label class="form-label fw-bold small text-dark mb-1">
                                                <i class="fas fa-comment-alt text-primary me-1"></i> Pesan Baru (Untuk Semua)
                                            </label>
                                            <textarea name="global_message" class="form-control form-control-sm" rows="3" placeholder="Tulis pesan baru untuk semua penerima..."></textarea>
                                            <div class="form-text x-small text-muted mt-1">Gunakan <code>{nama}</code> untuk auto-replace.</div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 text-secondary opacity-25">

                                <div class="alert alert-light border border-light-subtle mb-0">
                                    <small class="d-block fw-bold text-dark mb-2">Daftar Penerima:</small>
                                    
                                    <!-- LIST PREVIEW -->
                                    <div class="resend-preview-list bg-white">
                                        @foreach($batch->messages as $msg)
                                        <div class="resend-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <span class="font-monospace fw-bold me-2 text-dark">{{ $msg->phone }}</span>
                                                <span class="text-muted small text-truncate" style="max-width: 250px;">(Pesan Lama: {{ Str::limit($msg->content, 30) }})</span>
                                            </div>
                                            <div class="text-end">
                                                @if($msg->status == 'sent') <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle" style="font-size: 0.7rem;">Terkirim</span>
                                                @else <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.7rem;">Gagal</span> @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted mt-2 d-block text-end">Total: {{ count($batch->messages) }} Pesan</small>
                                </div>
                            </div>
                            
                            <div class="modal-footer border-top bg-white justify-content-between px-4 py-3">
                                <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#folderModal{{ $batch->id }}">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Ke Semua
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <!-- End Modal 2 -->

        </div>
        @empty
        <div class="col-12"><div class="alert alert-light border border-dashed text-center text-muted p-5 rounded-4"><i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i><p class="mb-0">Belum ada batch Excel yang diupload.</p></div></div>
        @endforelse
    </div>

    <!-- PESAN MANUAL -->
    <div class="d-flex align-items-center mb-3 mt-5">
        <h6 class="fw-bold text-secondary text-uppercase small ls-1 mb-0"><i class="fas fa-keyboard me-2"></i> Input Manual</h6>
        <div class="flex-grow-1 border-bottom ms-3"></div>
    </div>
    
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead><tr><th class="ps-4">Tujuan</th><th>Pesan</th><th>Jatuh Tempo</th><th>Status</th><th class="text-end pe-4">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($manualMessages as $msg)
                        <tr>
                            <td class="ps-4 fw-bold font-monospace text-dark">{{ $msg->phone }}</td>
                            <td><div class="text-truncate text-secondary" style="max-width: 350px;">{{ $msg->content }}</div></td>
                            <td>@if($msg->due_date) <div class="d-flex align-items-center"><i class="far fa-calendar-alt text-primary me-2"></i><div><span class="fw-bold text-dark">{{ $msg->due_date->format('d M Y, H:i') }}</span><br><small class="text-muted">{{ $msg->due_date->diffForHumans() }}</small></div></div> @else <span class="text-muted small">-</span> @endif</td>
                            <td>@if($msg->status == 'pending') <span class="badge bg-warning text-dark rounded-pill px-3">Pending</span> @elseif($msg->status == 'sent') <span class="badge bg-success rounded-pill px-3">Sent</span> @else <span class="badge bg-danger rounded-pill px-3">Failed</span> @endif</td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <form action="{{ route('sms.resend', $msg->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-light btn-sm text-primary border rounded-start shadow-sm"><i class="fas fa-paper-plane"></i></button></form>
                                    <form action="{{ route('sms.delete', $msg->id) }}" method="POST" onsubmit="return confirm('Hapus?');" class="d-inline">@csrf @method('DELETE')<button class="btn btn-light btn-sm text-danger border rounded-end shadow-sm"><i class="fas fa-trash-alt"></i></button></form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-list fa-2x mb-3 opacity-25"></i><p class="mb-0">Tidak ada jadwal manual.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">{{ $manualMessages->links('pagination::bootstrap-5') }}</div>
    </div>

    @endif
</div>
@endsection