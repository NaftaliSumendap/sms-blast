@extends('layouts.bar')

@section('title', 'Tulis Pesan - KIRIMPESAN')
@section('breadcrumb', 'Tulis Pesan')

@section('content')
<style>
    /* Style Toggle & Form */
    .nav-pills-toggle { background-color: #f1f3f4; border-radius: 50rem; padding: 4px; display: inline-flex; border: 1px solid #e0e0e0; }
    .nav-pills-toggle .nav-link { color: #5f6368; border-radius: 50rem; padding: 6px 20px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s ease; }
    .nav-pills-toggle .nav-link.active { background-color: #fff; color: #198754; box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-weight: 600; }
    .upload-area { border: 2px dashed #e0e0e0; border-radius: 12px; padding: 3rem 1rem; text-align: center; background-color: #fafafa; transition: all 0.3s; position: relative; }
    .upload-area:hover { border-color: #198754; background-color: #f8fffb; }
    .upload-icon-circle { width: 64px; height: 64px; background-color: #198754; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 1.8rem; box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2); }
    .form-label-custom { font-weight: 600; color: #5f6368; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .form-control-custom, .form-select-custom { border: 1px solid #dadce0; border-radius: 6px; padding: 0.6rem 1rem; font-size: 0.95rem; }
    .form-control-custom:focus, .form-select-custom:focus { border-color: #198754; box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1); }
    .reminder-card { background-color: #fff9e6; border: 1px solid #ffeeba; border-left: 4px solid #ffc107; border-radius: 6px; }
</style>

<div class="container-fluid px-0">

    @if(session('success'))
      <div class="alert alert-success d-flex align-items-center shadow-sm border-0 rounded-3 mb-4 mx-3" role="alert">
        <i class="fas fa-check-circle fs-4 me-3"></i> <div>{{ session('success') }}</div>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 rounded-3 mb-4 mx-3" role="alert">
        <i class="fas fa-exclamation-triangle fs-4 me-3"></i> <div>{{ session('error') }}</div>
      </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mx-3">
        <div class="card-body p-4">
            
            <!-- Header & Toggle -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-paper-plane text-success me-2 fs-4"></i> Kirim Pesan Baru
                </h5>

                <ul class="nav nav-pills nav-pills-toggle" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab">
                            <i class="fas fa-keyboard me-1"></i> Isi Manual
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-excel-tab" data-bs-toggle="pill" data-bs-target="#pills-excel" type="button" role="tab">
                            <i class="fas fa-file-excel me-1"></i> Upload Excel
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="pills-tabContent">

                <!-- TAB: UPLOAD EXCEL (Action ke sms.preview) -->
                <div class="tab-pane fade show active" id="pills-excel" role="tabpanel">
                    <form method="POST" action="{{ route('sms.preview') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="upload-area mb-4">
                            <div class="upload-icon-circle"><i class="fas fa-cloud-upload-alt"></i></div>
                            <h5 class="fw-bold mb-1">Upload File Excel</h5>
                            <p class="text-muted small mb-3">Format: .xlsx, .xls</p>
                            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold btn-sm" onclick="document.getElementById('excelFile').click()">Pilih File...</button>
                            <span id="fileName" class="ms-2 text-success fw-bold small"></span>
                            <input class="d-none" type="file" id="excelFile" name="file" accept=".xlsx,.xls" required onchange="document.getElementById('fileName').textContent = this.files[0].name">
                        </div>

                        <div class="row g-4 mb-4">
                            <!-- KOLOM NAMA (Untuk Buku Telepon) -->
                            <div class="col-md-3">
                                <label class="form-label-custom">Kolom Nama</label>
                                <select class="form-select form-select-custom" name="kolom_nama">
                                    @foreach(range('A', 'Z') as $char) <option value="{{ $char }}" {{ $char == 'A' ? 'selected' : '' }}>{{ $char }}</option> @endforeach
                                </select>
                            </div>

                            <!-- KOLOM NOMOR HP -->
                            <div class="col-md-3">
                                <label class="form-label-custom">Kolom No HP</label>
                                <select class="form-select form-select-custom" name="kolom_hp">
                                    @foreach(range('A', 'Z') as $char) <option value="{{ $char }}" {{ $char == 'B' ? 'selected' : '' }}>{{ $char }}</option> @endforeach
                                </select>
                            </div>
                            
                            <!-- KOLOM BARANG JAMINAN (Opsional) -->
                            <div class="col-md-3">
                                <label class="form-label-custom">Kolom Jaminan</label>
                                <select class="form-select form-select-custom" name="kolom_jaminan">
                                    <option value="" selected>-- Tidak Ada --</option>
                                    @foreach(range('A', 'Z') as $char) <option value="{{ $char }}">{{ $char }}</option> @endforeach
                                </select>
                            </div>

                            <!-- BARIS MULAI -->
                            <div class="col-md-3">
                                <label class="form-label-custom">Mulai Baris Ke-</label>
                                <select class="form-select form-select-custom" name="baris_mulai">
                                    <option value="2" selected>2 (Ada Header)</option>
                                    <option value="1">1</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Template Pesan</label>
                            <textarea class="form-control form-control-custom" name="template" rows="4" placeholder="Contoh: Halo {nama}, tagihan sebesar {tagihan} jatuh tempo besok." required></textarea>
                        </div>
                        <div class="d-flex align-items-center border rounded p-2 mb-4 bg-light">
                            <i class="far fa-lightbulb text-warning mx-2"></i>
                            <small class="text-muted">Gunakan <code class="text-danger">{nama_kolom_excel}</code> untuk data dinamis.</small>
                        </div>

                        <div class="reminder-card p-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;"><i class="fas fa-bell small"></i></div>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">Pengingat (Dari Excel)</span>
                            </div>
                            <div class="row align-items-center">
                                <label class="col-auto col-form-label text-muted small">Pilih Kolom Tanggal:</label>
                                <div class="col">
                                    <select class="form-select form-select-sm bg-white border-warning" name="kolom_due_date" style="max-width: 200px;">
                                        <option value="" selected>-- Tidak Ada --</option>
                                        @foreach(range('A', 'Z') as $char) <option value="{{ $char }}">Kolom {{ $char }}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-end border-top pt-3">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="fas fa-eye me-2"></i> Tinjau Pesan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB: ISI MANUAL (Action ke sms.send) -->
                <div class="tab-pane fade" id="pills-manual" role="tabpanel">
                    <form method="POST" action="{{ route('sms.send') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label-custom">Nomor Tujuan</label>
                            <textarea name="phone" rows="3" class="form-control form-control-custom" placeholder="Contoh: 08123456789,08123456780" required></textarea>
                            <small class="text-muted">Pisahkan nomor dengan koma (,).</small>
                        </div>
                        <div class="mb-4">
                            <label class="form-label-custom">Isi Pesan</label>
                            <textarea name="message" rows="5" class="form-control form-control-custom" placeholder="Tulis pesan Anda..." required></textarea>
                        </div>
                        <div class="reminder-card p-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px;"><i class="fas fa-bell small"></i></div>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">Atur Pengingat Manual</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6"><input type="datetime-local" name="due_date" class="form-control form-control-sm bg-white border-warning"></div>
                            </div>
                        </div>
                        <div class="text-end border-top pt-3">
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Pesan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection