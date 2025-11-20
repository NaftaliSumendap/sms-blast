<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SMS Blast Dashboard')</title>

    <!-- 1. Muat Bootstrap CSS dari CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 2. Muat Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

    <!-- 3. CSS Kustom -->
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }
        
        /* --- SIDEBAR STYLE PREMIUM --- */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #008f53; 
            background-image: linear-gradient(160deg, #008f53 0%, #007a45 100%);
            padding-top: 0;
            transition: margin-left 0.3s ease-in-out;
            z-index: 1030;
            box-shadow: 5px 0 15px rgba(0,0,0,0.05);
            overflow-y: auto; 
            display: flex;
            flex-direction: column;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }

        .sidebar .sidebar-header {
            padding: 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        .sidebar .sidebar-header .navbar-brand img {
            height: 70px; 
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
            transition: transform 0.3s;
        }
        .sidebar .sidebar-header .navbar-brand img:hover { transform: scale(1.05); }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 24px;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 5px solid transparent;
            display: flex;
            align-items: center;
            letter-spacing: 0.3px;
        }

        .sidebar .nav-link i {
            width: 35px;
            font-size: 1.1rem;
            text-align: left;
            opacity: 0.9;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.08);
            padding-left: 28px;
        }

        .sidebar .nav-link.active {
            background-color: rgba(0, 0, 0, 0.2);
            color: #fff;
            border-left-color: #fff;
            font-weight: 600;
            box-shadow: inset 10px 0 20px -10px rgba(0,0,0,0.2);
        }

        .sidebar .nav-title {
            padding: 1.5rem 24px 0.5rem;
            font-size: 0.75rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .mt-auto { margin-top: auto !important; }
        
        .btn-logout-custom {
            border: 1px solid rgba(255, 255, 255, 0.6);
            color: #fff;
            background: transparent;
            padding: 10px 20px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-logout-custom:hover {
            background-color: #fff;
            color: #007a45;
            border-color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* --- CONTENT & NAVBAR --- */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            transition: margin-left 0.3s ease-in-out;
            width: calc(100% - 280px);
        }
        .sidebar.collapsed { margin-left: -280px; }
        .main-content.full-width { margin-left: 0; width: 100%; }

        .navbar-custom {
            background-color: #fff;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0,0,0,0.01);
        }
        .btn-icon-soft {
            width: 45px; height: 45px;
            display: flex; align-items: center; justify-content: center;
            background-color: #f4f6f8; color: #555;
            border-radius: 12px; border: none;
            transition: all 0.2s ease;
        }
        .btn-icon-soft:hover { background-color: #e1e6eb; color: #008f53; }

        .breadcrumb-item a { color: #008f53; text-decoration: none; font-weight: 600; }
        .breadcrumb-item.active { color: #888; }
        .breadcrumb-item + .breadcrumb-item::before { content: "/"; color: #adb5bd; }
        
        /* --- Toast & Notification --- */
        .notification-badge { position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; background-color: #dc3545; color: white; font-size: 0.65rem; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }
        
        .dropdown-menu-notify { width: 320px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.1); border-radius: 16px; overflow: hidden; }
        .dropdown-header-notify { background-color: #008f53; color: white; padding: 1rem; font-weight: bold; }
        .notify-item { padding: 12px 20px; border-bottom: 1px solid #eee; display: block; text-decoration: none; color: #333; transition:0.2s; }
        .notify-item:hover { background-color: #f8f9fa; }
        .notify-time { font-size: 0.75rem; color: #888; }

        .modal-logout .modal-content { border-radius: 24px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.15); }
        .logout-icon { background-color: #fff8e1; color: #ffc107; width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 3rem; }
        
        .toast-welcome { animation: slideInLeft 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
        @keyframes slideInLeft { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body>

    <!-- 1. TOAST NOTIFIKASI -->
    @if (session('status'))
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <div id="welcomeToast" class="toast align-items-center text-white bg-success border-0 toast-welcome shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fs-6">
                    <i class="fas fa-check-circle me-2"></i> {!! session('status') !!} 
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="d-flex">
        <!-- 2. SIDEBAR PREMIUM -->
        <nav class="sidebar" id="sidebar">
            
            <!-- Header Logo -->
            <div class="sidebar-header">
                <a class="navbar-brand" href="/beranda">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Aplikasi">
                </a>
            </div>
            
            <ul class="nav flex-column w-100">
                <!-- Menu Utama -->
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('beranda') || request()->is('/')) ? 'active' : '' }}" href="/beranda">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tulis-pesan') ? 'active' : '' }}" href="/tulis-pesan">
                        <i class="fas fa-paper-plane"></i> Tulis Pesan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pengiriman-terjadwal') ? 'active' : '' }}" href="/pengiriman-terjadwal">
                        <i class="fas fa-clock"></i> Pengiriman Terjadwal
                    </a>
                </li>
                 <!-- <li class="nav-item">
                    <a class="nav-link {{ request()->is('balas-otomatis') ? 'active' : '' }}" href="/balas-otomatis">
                        <i class="fas fa-robot"></i> Balas Otomatis
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('riwayat-pesan') ? 'active' : '' }}" href="/riwayat-pesan">
                        <i class="fas fa-history"></i> Riwayat Pesan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('buku-telepon') ? 'active' : '' }}" href="/buku-telepon">
                        <i class="fas fa-address-book"></i> Buku Telepon
                    </a>
                </li>

                <!-- Bagian Info (Label) -->
                <li class="nav-title">Info</li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('syarat-ketentuan') ? 'active' : '' }}" href="/syarat-ketentuan">
                        <i class="fas fa-file-contract"></i> Syarat & Ketentuan
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link {{ request()->is('tentang-aplikasi') ? 'active' : '' }}" href="/tentang-aplikasi">
                        <i class="fas fa-info-circle"></i> Tentang Aplikasi
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link {{ request()->is('update-aplikasi') ? 'active' : '' }}" href="/update-aplikasi">
                        <i class="fas fa-sync-alt"></i> Update Aplikasi
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link {{ request()->is('bantuan') ? 'active' : '' }}" href="/bantuan">
                        <i class="fas fa-question-circle"></i> Bantuan
                    </a>
                </li>
            </ul>

            <!-- Tombol Logout di Bagian Paling Bawah -->
            <div class="mt-auto p-4 w-100">
                <button type="button" class="btn-logout-custom" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </nav>

        <!-- 3. KONTEN UTAMA -->
        <main class="main-content flex-grow-1" id="main-content">
            
            <!-- Navbar Atas -->
            <nav class="navbar-custom mb-4 d-flex justify-content-between align-items-center">
                 <div class="d-flex align-items-center">
                    <button class="btn-icon-soft me-3" id="sidebar-toggle" title="Toggle Sidebar">
                        <i class="fas fa-bars fs-5"></i>
                    </button>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="/beranda">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb', 'Halaman')</li>
                        </ol>
                    </nav>
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Dropdown Notifikasi -->
                    <div class="dropdown">
                        <button class="btn-icon-soft position-relative text-decoration-none border-0" type="button" id="notifyDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                            <i class="far fa-bell fs-5"></i>
                            @if(isset($notificationCount) && $notificationCount > 0)
                                <span class="notification-badge">{{ $notificationCount }}</span>
                            @endif
                        </button>

                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-notify" aria-labelledby="notifyDropdown">
                            <div class="dropdown-header-notify d-flex justify-content-between align-items-center">
                                <span>Jatuh Tempo Hari Ini</span>
                                @if(isset($notificationCount) && $notificationCount > 0)
                                    <span class="badge bg-white text-success rounded-pill">{{ $notificationCount }}</span>
                                @endif
                            </div>
                            
                            <div style="max-height: 300px; overflow-y: auto;">
                                @if(isset($todayNotifications) && count($todayNotifications) > 0)
                                    @foreach($todayNotifications as $notif)
                                    <a href="{{ $notif->link }}" class="notify-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            
                                            {{-- LOGIKA TAMPILAN: FOLDER vs MANUAL --}}
                                            @if($notif->type == 'batch')
                                                <!-- Tampilan Folder -->
                                                <div class="d-flex align-items-center text-truncate" style="max-width: 75%;">
                                                    <i class="fas fa-folder text-warning me-2 fs-5"></i>
                                                    <strong class="text-dark text-truncate">{{ $notif->title }}</strong>
                                                </div>
                                            @else
                                                <!-- Tampilan Manual -->
                                                <strong class="text-truncate" style="max-width: 75%;">{{ $notif->title }}</strong>
                                            @endif

                                            <span class="notify-time text-nowrap ms-2">
                                                <i class="far fa-clock me-1"></i> {{ $notif->time->format('H:i') }}
                                            </span>
                                        </div>
                                        
                                        <div class="text-muted small text-truncate mt-1" style="max-width: 280px;">
                                            @if($notif->type == 'batch')
                                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-0 me-1" style="font-size: 0.7rem;">Batch</span>
                                            @endif
                                            {{ $notif->desc }}
                                        </div>
                                    </a>
                                    @endforeach
                                @else
                                    <div class="text-center p-5 text-muted">
                                        <i class="fas fa-check-circle fa-3x mb-3 text-light"></i>
                                        <p class="mb-0 small">Tidak ada pesan jatuh tempo hari ini.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="text-center p-3 border-top bg-light">
                                <a href="{{ route('pengiriman-terjadwal') }}" class="small text-decoration-none fw-bold text-success">Lihat Semua Jadwal</a>
                            </div>
                        </div>
                    </div>

                    <!-- Profil Avatar -->
                    <div class="ms-3 d-none d-md-block">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-weight: bold; font-size: 1.1rem;">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            @yield('content')

        </main>
    </div>

    <!-- 4. MODAL LOGOUT -->
    <div class="modal fade modal-logout" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-5">
                    <div class="logout-icon">👋</div>
                    <h3 class="fw-bold mb-3">Sampai Jumpa!</h3>
                    <p class="text-muted mb-4 fs-6">Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?</p>
                </div>
                <div class="modal-footer justify-content-center gap-3 pb-4">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-pill fw-bold text-secondary border" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-5 py-2 rounded-pill fw-bold shadow-sm">Ya, Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleButton = document.getElementById('sidebar-toggle');

            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('full-width');
                });
            }
        });
    </script>

    @if (session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.getElementById('welcomeToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
                toast.show();
            }
        });
    </script>
    @endif
    
    @yield('scripts')
</body>
</html>