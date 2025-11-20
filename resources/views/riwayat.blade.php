@extends('layouts.bar')

@section('title', 'Riwayat Pesan - KIRIMPESAN')
@section('breadcrumb', 'Riwayat Pesan')

@section('content')
<div class="container-fluid">
    
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
                                <h2 class="fw-bold mb-0 text-dark">{{ array_sum($chartData) }}</h2>
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
                                <h2 class="fw-bold mb-0 text-success">{{ $chartData['sent'] }}</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending & Gagal (Digabung agar hemat tempat) --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 bg-white">
                        <div class="card-body d-flex flex-column justify-content-center p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold"><i class="fas fa-clock text-warning me-1"></i> Pending</span>
                                <span class="fw-bold text-dark">{{ $chartData['pending'] }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ array_sum($chartData) > 0 ? ($chartData['pending'] / array_sum($chartData)) * 100 : 0 }}%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small fw-bold"><i class="fas fa-times-circle text-danger me-1"></i> Gagal</span>
                                <span class="fw-bold text-dark">{{ $chartData['failed'] }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ array_sum($chartData) > 0 ? ($chartData['failed'] / array_sum($chartData)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Grafik (Chart.js) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body position-relative">
                    <h6 class="card-title fw-bold text-dark mb-3">Persentase Pengiriman</h6>
                    <div style="height: 200px; position: relative;">
                        <canvas id="smsStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BAGIAN TABEL RIWAYAT -->
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history text-success me-2"></i> Log Pengiriman</h5>
            <a href="{{ route('riwayat-pesan') }}" class="btn btn-light btn-sm border rounded-pill hover-shadow">
                <i class="fas fa-sync-alt me-1"></i> Segarkan
            </a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="px-4 py-3 border-0" width="5%">#</th>
                            <th class="px-4 py-3 border-0" width="15%">Waktu</th>
                            <th class="px-4 py-3 border-0" width="15%">Tujuan</th>
                            <th class="px-4 py-3 border-0" width="35%">Pesan</th>
                            <th class="px-4 py-3 border-0" width="15%">Jadwal</th>
                            <th class="px-4 py-3 border-0 text-center" width="15%">Status</th>
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
                            <td colspan="6" class="text-center py-5 text-muted">
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
@endsection

@section('scripts')
<!-- 1. Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 2. Siapkan Data dari Controller
        const chartData = {
            sent: {{ $chartData['sent'] }},
            pending: {{ $chartData['pending'] }},
            failed: {{ $chartData['failed'] }}
        };

        // 3. Render Chart
        const ctx = document.getElementById('smsStatusChart').getContext('2d');
        
        // Jika tidak ada data sama sekali, tampilkan pesan atau chart kosong
        const totalData = chartData.sent + chartData.pending + chartData.failed;

        new Chart(ctx, {
            type: 'doughnut', // Jenis chart: Donat
            data: {
                labels: ['Berhasil', 'Pending', 'Gagal'],
                datasets: [{
                    data: [chartData.sent, chartData.pending, chartData.failed],
                    backgroundColor: [
                        '#198754', // Hijau (Success)
                        '#ffc107', // Kuning (Warning)
                        '#dc3545'  // Merah (Danger)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Ketebalan donat
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                let percentage = totalData > 0 ? Math.round((value / totalData) * 100) + '%' : '0%';
                                return label + ': ' + value + ' (' + percentage + ')';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection