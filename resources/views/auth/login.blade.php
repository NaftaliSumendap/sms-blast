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

    <style>
        body {
            background-color: #f4f6f9;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            border-radius: 0.75rem;
        }
        .login-logo {
            max-height: 80px; /* Anda bisa sesuaikan ukurannya */
            width: auto;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="login-container p-3">
        <div class="card shadow-lg login-card">
            <div class="card-body p-4 p-md-5">
                
                <div class="text-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
                    <h3 class="fw-bold mb-4">Selamat Datang</h3>
                </div>

                {{-- Menampilkan notifikasi error jika login gagal --}}
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- Ganti action ke route login Anda --}}
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control form-control-lg" id="email" name="email" placeholder="Masukkan email" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Masukkan password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        <a href="#" class="text-success text-decoration-none">Lupa Password?</a>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-sign-in-alt me-2"></i> Login</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>