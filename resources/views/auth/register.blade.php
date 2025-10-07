<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun baru di platform pembelajaran inklusif Inkluvia">
    <title>Register - Inkluvia</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#513587',
                        secondary: '#A7AAFF'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-['Inter']">
    <!-- Skip to main content -->
    <a href="#register-form" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-primary text-white px-4 py-2 rounded">
        Skip to registration form
    </a>

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center text-3xl font-bold text-primary mb-8 hover:opacity-80 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded"
                   aria-label="Kembali ke beranda Inkluvia">
                    <img src="{{ asset('assets/icon.png') }}" alt="Inkluvia Logo" class="w-12 h-10 mr-3">
                    inkluvia
                </a>

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Bergabung dengan Inkluvia</h1>
                    <p class="text-gray-600">Daftar untuk memulai perjalanan belajar Anda</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div role="alert" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <ul class="text-sm text-red-800 space-y-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="register-form" class="space-y-5">
                    @csrf
                    
                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-semibold text-gray-900 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nama_lengkap" 
                            name="nama_lengkap" 
                            required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Masukkan nama lengkap"
                            value="{{ old('nama_lengkap') }}"
                            aria-required="true">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Masukkan email"
                            value="{{ old('email') }}"
                            aria-required="true">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="tanggal_lahir" 
                            name="tanggal_lahir" 
                            required
                            max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            value="{{ old('tanggal_lahir') }}"
                            aria-required="true">
                    </div>

                    <!-- Jenis Kelamin -->
                    <fieldset>
                        <legend class="block text-sm font-semibold text-gray-900 mb-3">
                            Jenis Kelamin <span class="text-red-500">*</span>
                        </legend>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <input 
                                    type="radio" 
                                    id="laki_laki" 
                                    name="jenis_kelamin" 
                                    value="Laki-laki"
                                    {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}
                                    required
                                    class="peer sr-only">
                                <label for="laki_laki" 
                                       class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                    <i class="fas fa-mars text-primary mr-2" aria-hidden="true"></i>
                                    <span class="font-medium text-gray-900">Laki-laki</span>
                                    <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                                </label>
                            </div>
                            
                            <div class="relative">
                                <input 
                                    type="radio" 
                                    id="perempuan" 
                                    name="jenis_kelamin" 
                                    value="Perempuan"
                                    {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}
                                    class="peer sr-only">
                                <label for="perempuan" 
                                       class="flex items-center justify-center p-3 border-2 border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-primary peer-checked:bg-primary peer-checked:bg-opacity-10 peer-focus:ring-2 peer-focus:ring-primary peer-focus:ring-offset-2 transition-all">
                                    <i class="fas fa-venus text-primary mr-2" aria-hidden="true"></i>
                                    <span class="font-medium text-gray-900">Perempuan</span>
                                    <i class="fas fa-check-circle text-primary ml-auto opacity-0 peer-checked:opacity-100 transition-opacity" aria-hidden="true"></i>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Lembaga -->
                    <div>
                        <label for="lembaga_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Sekolah/Lembaga <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="lembaga_id" 
                            name="lembaga_id" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base bg-white"
                            aria-required="true">
                            <option value="">Pilih Sekolah/Lembaga</option>
                            @foreach($lembagas as $lembaga)
                            <option value="{{ $lembaga->id }}" data-type="{{ $lembaga->type }}" {{ old('lembaga_id') == $lembaga->id ? 'selected' : '' }}>
                                {{ $lembaga->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <div id="lembaga_key_wrapper" style="display: none;">
                        <label for="lembaga_key" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kunci Lembaga
                        </label>
                        <input 
                            type="text" 
                            id="lembaga_key" 
                            name="lembaga_key" 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Masukkan kunci lembaga"
                            value="{{ old('lembaga_key') }}">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Minimal 8 karakter"
                            aria-required="true"
                            aria-describedby="password-help">
                        <p id="password-help" class="mt-1 text-xs text-gray-500">
                            Password minimal 8 karakter
                        </p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-900 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                            placeholder="Ulangi password"
                            aria-required="true">
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none"
                        aria-label="Daftar akun baru">
                        <i class="fas fa-user-plus mr-2" aria-hidden="true"></i>
                        Daftar Sekarang
                    </button>
                </form>

                <!-- Login Link -->
                <p class="mt-6 text-center text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" 
                       class="text-primary font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded"
                       aria-label="Login ke akun yang sudah ada">
                        Masuk di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex flex-1 bg-gradient-to-br from-primary to-secondary items-center justify-center p-12">
            <div class="max-w-lg text-center" role="img" aria-label="Ilustrasi pembelajaran braille">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-3xl p-8 mb-6">
                    <img src="{{ asset('assets/auth.png') }}" 
                        alt="Ilustrasi pembelajaran braille" 
                        class="w-full h-auto rounded-2xl shadow-lg">
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Mulai Perjalanan Belajar Anda</h2>
                <p class="text-white text-opacity-90">Bergabung dengan ribuan pelajar lainnya dalam platform pembelajaran inklusif</p>
            </div>
        </div>
    </div>

    <!-- Live Region for Screen Reader Announcements -->
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

    <style>
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
        
        .sr-only.focus\:not-sr-only:focus {
            position: static;
            width: auto;
            height: auto;
            padding: 0.5rem 1rem;
            margin: 0;
            overflow: visible;
            clip: auto;
            white-space: normal;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus management
            const namaInput = document.getElementById('nama_lengkap');
            if (namaInput && !namaInput.value) {
                namaInput.focus();
            }

            // Password confirmation validation
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            
            function validatePassword() {
                if (password.value && passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Password tidak sama');
                } else {
                    passwordConfirmation.setCustomValidity('');
                }
            }
            
            password.addEventListener('input', validatePassword);
            passwordConfirmation.addEventListener('input', validatePassword);

            const lembagaSelect = document.getElementById('lembaga_id');
            const keyWrapper = document.getElementById('lembaga_key_wrapper');
            function toggleKey() {
                const opt = lembagaSelect.options[lembagaSelect.selectedIndex];
                const type = opt && opt.dataset ? opt.dataset.type : '';
                keyWrapper.style.display = (lembagaSelect.value && type !== 'Individu') ? 'block' : 'none';
            }
            lembagaSelect.addEventListener('change', toggleKey);
            toggleKey();

            // Form submission announcement
            const form = document.getElementById('register-form');
            form.addEventListener('submit', function() {
                document.getElementById('announcements').textContent = 'Memproses pendaftaran...';
            });

            // Announce page load
            setTimeout(() => {
                document.getElementById('announcements').textContent = 'Halaman pendaftaran. Isi formulir untuk membuat akun baru.';
            }, 500);
        });
    </script>
</body>
</html>