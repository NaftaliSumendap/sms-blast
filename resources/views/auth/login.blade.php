<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KIRIMPESAN</title>

    <!-- Muat Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Muat Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <!-- Google Font (Opsional, agar lebih mirip) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            /* Latar Belakang Hijau dengan Gradasi Lembut */
            background-color: #008f53; 
            background-image: linear-gradient(135deg, #e8f5e9 0%, #d4edda 100%); /* Gradasi lembut hijau muda */
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px; 
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15); /* Bayangan lebih tegas */
            border: none;
            padding: 2.5rem 2rem;
            position: relative; /* Penting untuk efek 3D */
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background-color: #198754; /* Garis hijau di atas kartu */
        }

        .login-logo {
            height: 65px;
            width: auto;
            margin-bottom: 1rem;
        }

        .login-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 2rem;
            font-size: 1.5rem;
        }

        /* Styling Input agar Ikon terlihat menyatu */
        .input-group-text {
            background-color: #fff;
            border-right: none;
            color: #6c757d;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
            border-color: #dee2e6;
        }
        
        .form-control {
            border-left: none;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            border-color: #dee2e6;
            padding: 0.7rem 1rem 0.7rem 0;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #198754;
        }

        .input-group:focus-within .input-group-text,
        .form-control:focus + .input-group-text {
            border-color: #198754;
            color: #198754;
        }
        
        .input-group:focus-within .form-control {
            border-color: #198754;
        }

        /* Label Form */
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        /* Tombol Login */
        .btn-login {
            background-color: #198754;
            background-image: linear-gradient(to right, #198754, #157347);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.8rem;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-image: linear-gradient(to right, #157347, #0f5132);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
            color: #fff;
        }

        .forgot-password {
            font-size: 0.85rem;
            color: #198754;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-password:hover {
            text-decoration: underline;
            color: #0f5132;
        }
        
        .form-check-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
    </style>
</head>
<body>

    <div class="login-card">
        
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Pegadaian" class="login-logo">
            <h3 class="login-title">Selamat Datang</h3>
        </div>

        {{-- Menampilkan notifikasi error jika login gagal --}}
        @if(session('error'))
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center mb-4 rounded-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> 
                <small>{{ session('error') }}</small>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <!-- Input Email -->
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                </div>
            </div>

            <!-- Input Password -->
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <!-- Ingat Saya & Lupa Password -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        Ingat Saya
                    </label>
                </div>
                <a href="#" class="forgot-password">Lupa Password?</a>
            </div>

            <!-- Tombol Login -->
            <div class="d-grid">
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>