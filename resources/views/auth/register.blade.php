<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Inkluvia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <a href="{{ route('home') }}" class="logo">Inkluvia</a>
            
            <h1 class="auth-title">Bergabung dengan Inkluvia</h1>
            <p class="auth-subtitle">Daftar untuk memulai perjalanan belajar Anda</p>

            @if($errors->any())
                <div class="error-message">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="form-group">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="nama_lengkap" 
                        name="nama_lengkap" 
                        class="form-input" 
                        placeholder="Masukkan nama lengkap Anda"
                        value="{{ old('nama_lengkap') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input 
                        type="date" 
                        id="tanggal_lahir" 
                        name="tanggal_lahir" 
                        class="form-input" 
                        value="{{ old('tanggal_lahir') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <div class="gender-group">
                        <label class="radio-option" for="laki-laki">
                            <input 
                                type="radio" 
                                id="laki-laki" 
                                name="jenis_kelamin" 
                                value="Laki-laki"
                                {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}
                                required
                            >
                            Laki-laki
                        </label>
                        <label class="radio-option" for="perempuan">
                            <input 
                                type="radio" 
                                id="perempuan" 
                                name="jenis_kelamin" 
                                value="Perempuan"
                                {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}
                                required
                            >
                            Perempuan
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lembaga_id" class="form-label">Sekolah/Lembaga</label>
                    <select id="lembaga_id" name="lembaga_id" class="form-select" required>
                        <option value="">Pilih Sekolah/Lembaga</option>
                        @foreach($lembagas as $lembaga)
                            <option value="{{ $lembaga->id }}" 
                                {{ old('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
                                {{ $lembaga->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="Masukkan email Anda"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Minimal 8 karakter"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="Ulangi password Anda"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus" style="margin-right: 0.5rem;"></i>
                    Daftar Sekarang
                </button>
            </form>

            <div class="auth-link">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>

        <div class="auth-illustration">
            <img src="{{ asset('assets/register-illustration.png') }}" alt="Ilustrasi Register" 
                 onerror="this.style.display='none'">
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus management
            const namaInput = document.getElementById('nama_lengkap');
            if (namaInput) {
                namaInput.focus();
            }

            // Radio button styling
            const radioOptions = document.querySelectorAll('.radio-option');
            radioOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                
                if (radio.checked) {
                    option.classList.add('selected');
                }
                
                radio.addEventListener('change', function() {
                    radioOptions.forEach(opt => opt.classList.remove('selected'));
                    if (this.checked) {
                        option.classList.add('selected');
                    }
                });
            });
        });
    </script>
</body>
</html>