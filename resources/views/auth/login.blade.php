<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke platform pembelajaran inklusif Inkluvia">
    <title>Login - Inkluvia</title>
    
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
    <a href="#login-form" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 bg-primary text-white px-4 py-2 rounded">
        Lewati ke formulir login
    </a>

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <a href="{{ route('home') }}" 
                class="inline-flex items-center text-3xl font-bold text-primary mb-8 hover:opacity-80 transition-opacity focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded"
                aria-label="Kembali ke beranda Inkluvia">
                    
                    <img src="{{ asset('assets/icon.png') }}" alt="" class="w-12 h-10 mr-3" aria-hidden="true">
                    inkluvia
                </a>

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Login</h1>
                    <p class="text-gray-600">Masuk untuk melanjutkan pembelajaran</p>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                <div role="alert" aria-live="assertive" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3" aria-hidden="true"></i>
                        <div class="flex-1">
                            <p class="font-semibold text-sm text-red-800 mb-1">Terjadi kesalahan:</p>
                            <p class="text-sm text-red-800">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-6" novalidate>
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                            Email <span class="text-red-600" aria-label="wajib diisi">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400" aria-hidden="true"></i>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                                placeholder="contoh@email.com"
                                value="{{ old('email') }}"
                                aria-required="true"
                                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                aria-describedby="email-error">
                        </div>
                        @error('email')
                        <p id="email-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kata Sandi <span class="text-red-600" aria-label="wajib diisi">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400" aria-hidden="true"></i>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                autocomplete="current-password"
                                class="w-full pl-12 pr-12 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors text-base"
                                placeholder="Masukkan kata sandi"
                                aria-required="true"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                aria-describedby="password-error password-toggle-desc">
                            <button 
                                type="button" 
                                onclick="togglePassword('password', this)" 
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset rounded-r-lg"
                                aria-label="Tampilkan kata sandi"
                                aria-pressed="false">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <span id="password-toggle-desc" class="sr-only">Tekan tombol untuk menampilkan atau menyembunyikan kata sandi</span>
                        @error('password')
                        <p id="password-error" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-opacity-90 transition-all focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:outline-none">
                        Masuk
                    </button>
                </form>

                <!-- Register Link -->
                <p class="mt-6 text-center text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" 
                       class="text-primary font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded px-1">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex flex-1 bg-gradient-to-br from-primary to-secondary items-center justify-center p-12" aria-hidden="true">
            <div class="max-w-lg text-center">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-3xl p-8 mb-6">
                    <img src="{{ asset('assets/auth.png') }}" 
                        alt="" 
                        class="w-full h-auto rounded-2xl shadow-lg">
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Platform Pembelajaran Inklusif</h2>
                <p class="text-white text-opacity-90">Belajar Braille dengan mudah dan interaktif untuk semua</p>
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
        function togglePassword(inputId, buttonEl) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const isCurrentlyHidden = input.type === 'password';
            input.type = isCurrentlyHidden ? 'text' : 'password';

            if (buttonEl) {
                buttonEl.setAttribute('aria-pressed', isCurrentlyHidden ? 'true' : 'false');
                buttonEl.setAttribute('aria-label', isCurrentlyHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            }

            // Announce to screen readers
            const announcement = isCurrentlyHidden ? 'Kata sandi ditampilkan' : 'Kata sandi disembunyikan';
            document.getElementById('announcements').textContent = announcement;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Focus management
            const emailInput = document.getElementById('email');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }

            // Form validation
            const form = document.getElementById('login-form');
            const passwordInput = document.getElementById('password');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Email validation
                if (!emailInput.validity.valid) {
                    isValid = false;
                }
                
                // Password validation
                if (!passwordInput.value) {
                    isValid = false;
                }
                
                if (isValid) {
                    document.getElementById('announcements').textContent = 'Memproses login, mohon tunggu...';
                }
            });

            // Announce page load for screen readers
            setTimeout(() => {
                document.getElementById('announcements').textContent = 'Halaman login. Gunakan formulir untuk masuk ke akun Anda.';
            }, 500);
        });
    </script>
</body>
</html>