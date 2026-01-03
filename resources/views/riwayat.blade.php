@extends('layouts.bar')

@section('title', 'Riwayat Pesan - KIRIMPESAN')
@section('breadcrumb', 'Riwayat Pesan')

<style>
    /* Style untuk daftar batch di dalam modal */
    .batch-list-export {
        max-height: 200px;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
    }
</style>

@section('content')
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

    @php
        $totalData = array_sum($chartData);
        $calcPercentage = function($value) use ($totalData) {
            return $totalData > 0 ? round(($value / $totalData) * 100) : 0;
        };
    @endphp

    <!-- BAGIAN STATISTIK & CHART -->
    <div class="row mb-4">
        <!-- Kartu Ringkasan Angka -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="row g-3 h-100">
                {{-- Total Pesan --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-white">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Pesan</h6>
                                <h2 class="fw-bold mb-0 text-dark">{{ $totalData }}</h2>
                            </div>
                            <div class="bg-light rounded-circle p-3 text-primary">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Terkirim --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-white">
                        <div class="card-body d-flex align-items-center justify-content-between p-4">
                            <div>
                                <h6 class="text-muted text-uppercase fw-bold small mb-1">Berhasil</h6>
                                <h2 class="fw-bold mb-0 text-success">
                                    {{ $chartData['sent'] }} 
                                    <span class="small text-muted">({{ $calcPercentage($chartData['sent']) }}%)</span>
                                </h2>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending & Gagal (Digabung) --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-white">
                        <div class="card-body d-flex flex-column justify-content-center p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold"><i class="fas fa-clock text-warning me-1"></i> Pending</span>
                                <span class="fw-bold text-dark">
                                    {{ $chartData['pending'] }}
                                    <span class="small text-muted">({{ $calcPercentage($chartData['pending']) }}%)</span>
                                </span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ $calcPercentage($chartData['pending']) }}%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold"><i class="fas fa-times-circle text-danger me-1"></i> Gagal</span>
                                <span class="fw-bold text-dark">
                                    {{ $chartData['failed'] }}
                                    <span class="small text-muted">({{ $calcPercentage($chartData['failed']) }}%)</span>
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $calcPercentage($chartData['failed']) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Grafik (Chart.js) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body position-relative"><h6 class="card-title fw-bold text-dark mb-3">Persentase Pengiriman</h6><div style="height: 200px; position: relative;"><canvas id="smsStatusChart"></canvas></div></div>
            </div>
        </div>
    </div>

    <!-- BAGIAN TABEL RIWAYAT -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-success me-2"></i> Log Pengiriman</h5>
            
            {{-- TOMBOL DOWNLOAD BARU (MEMICU MODAL) --}}
            <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-download me-1"></i> Download Laporan
            </button>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 border-0" width="5%">#</th>
                            <th class="px-4 py-3 border-0" width="15%">Waktu</th>
                            <th class="px-4 py-3 border-0" width="15%">Tujuan</th>
                            <th class="px-4 py-3 border-0" width="30%">Pesan</th>
                            <th class="px-4 py-3 border-0" width="15%">Tipe Kirim</th>
                            <th class="px-4 py-3 border-0" width="10%">Jadwal</th>
                            <th class="px-4 py-3 border-0 text-center" width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $index => $msg)
                        <tr>
                            <td class="px-4 text-muted">{{ $messages->firstItem() + $index }}</td>
                            <td class="px-4 text-muted small fw-bold">
                                {{ $msg->created_at->format('d M Y') }}<br>
                                <span class="fw-normal text-secondary">{{ $msg->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 fw-bold text-dark font-monospace">{{ $msg->phone }}</td>
                            <td class="px-4 text-secondary">
                                <div class="text-truncate" style="max-width: 250px;" data-bs-toggle="tooltip" title="{{ $msg->content }}">
                                    {{ $msg->content }}
                                </div>
                            </td>
                            <td class="px-4 small">
                                @if($msg->message_batch_id)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1 fw-normal">BATCH</span>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;" data-bs-toggle="tooltip" title="{{ $msg->batch->batch_name ?? '-' }}">
                                        {{ Str::limit($msg->batch->batch_name ?? '-', 15) }}
                                    </div>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1 fw-normal">MANUAL</span>
                                @endif
                            </td>
                            <td class="px-4">
                                @if($msg->due_date)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill fw-normal">
                                        <i class="far fa-calendar-alt me-1"></i> {{ $msg->due_date->format('d M') }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="px-4 text-center">
                                @if($msg->status == 'sent')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Sent</span>
                                @elseif($msg->status == 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Pending</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Failed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Belum ada riwayat pesan.</p>
                                    <a href="{{ route('tulis-pesan') }}" class="btn btn-link text-decoration-none mt-2">Kirim pesan sekarang</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $messages->firstItem() ?? 0 }} - {{ $messages->lastItem() ?? 0 }} dari {{ $messages->total() }}
            </div>
            <div>{{ $messages->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL FILTER DAN DOWNLOAD LAPORAN (BARU) -->
<!-- ======================================================= -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('sms.export') }}" method="GET" class="modal-content rounded-4 border-0 shadow-lg">
            
            <div class="modal-header bg-white border-bottom-0 py-3 px-4">
                <h5 class="modal-title fw-bold" id="exportModalLabel"><i class="fas fa-file-export me-2 text-primary"></i> Filter Laporan Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <div class="alert alert-info small border-0 bg-info bg-opacity-10 text-info rounded-3">
                    <i class="fas fa-info-circle me-1"></i> Laporan akan berisi data terintegrasi (Nama Nasabah, Barang Jaminan, dan Status Kirim).
                </div>

                <!-- Pilihan Tipe Export -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-secondary">1. Pilih Tipe Data</h6>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_type" id="export_all" value="all" checked onchange="toggleBatchSelection(this.value)">
                                <label class="form-check-label" for="export_all">Semua Data (Manual + Batch)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_type" id="export_manual" value="manual" onchange="toggleBatchSelection(this.value)">
                                <label class="form-check-label" for="export_manual">Hanya Manual</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="export_type" id="export_batch" value="batch" onchange="toggleBatchSelection(this.value)">
                                <label class="form-check-label" for="export_batch">Pilih Batch (Excel)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rentang Waktu -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-secondary">2. Rentang Waktu Kirim</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="{{ now()->subMonths(1)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pemilihan Batch (Hanya Muncul jika 'Pilih Batch' dicentang) -->
                <div class="card border-0 shadow-sm" id="batchSelectionArea" style="display: none;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-secondary">3. Pilih Folder Batch yang Akan Diekspor</h6>
                        <div class="batch-list-export bg-white">
                            @forelse($allBatches as $batch)
                            <div class="form-check py-1">
                                <input class="form-check-input" type="checkbox" name="batch_ids[]" value="{{ $batch->id }}" id="batch_{{ $batch->id }}">
                                <label class="form-check-label" for="batch_{{ $batch->id }}">
                                    {{ Str::limit($batch->batch_name, 40) }} ({{ $batch->total_msg }} Pesan)
                                    <span class="text-muted small"> | Dibuat: {{ $batch->created_at->format('d M Y') }}</span>
                                </label>
                            </div>
                            @empty
                            <div class="text-center text-muted small py-3">Tidak ada folder batch yang tersedia.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="modal-footer bg-white justify-content-between">
                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                    <i class="fas fa-file-download me-2"></i> Download Laporan CSV
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<script>
    // Fungsi untuk menampilkan/menyembunyikan area pemilihan batch
    function toggleBatchSelection(type) {
        const area = document.getElementById('batchSelectionArea');
        const batchCheckboxes = area.querySelectorAll('input[type="checkbox"]');
        
        if (type === 'batch') {
            area.style.display = 'block';
            batchCheckboxes.forEach(cb => cb.required = true);
        } else {
            area.style.display = 'none';
            batchCheckboxes.forEach(cb => cb.required = false);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // 2. Siapkan Data dari Controller (Chart)
        const chartData = {
            sent: {{ $chartData['sent'] }},
            pending: {{ $chartData['pending'] }},
            failed: {{ $chartData['failed'] }}
        };
        const totalData = chartData.sent + chartData.pending + chartData.failed;
        
        // Render Chart
        if (document.getElementById('smsStatusChart')) {
            const ctx = document.getElementById('smsStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Berhasil', 'Pending', 'Gagal'],
                    datasets: [{
                        data: [chartData.sent, chartData.pending, chartData.failed],
                        backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                        borderWidth: 0, hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 15, font: { size: 11 } } },
                        tooltip: { callbacks: { label: function(context) { let label = context.label || ''; let value = context.raw || 0; let percentage = totalData > 0 ? Math.round((value / totalData) * 100) + '%' : '0%'; return label + ': ' + value + ' (' + percentage + ')'; } } }
                    }
                }
            });
        }
        
        // Inisialisasi Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });
        
        // Inisialisasi Toggle Batch Awal
        toggleBatchSelection(document.querySelector('input[name="export_type"]:checked')?.value || 'all');
    });
</script>
@endsection