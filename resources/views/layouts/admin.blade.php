<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Inkluvia</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#513587',
                            dark: '#3d2866',
                            light: '#6B46C1',
                        },
                        secondary: {
                            DEFAULT: '#A7AAFF',
                            light: '#C4B5FD',
                        }
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="bg-gray-50 font-['Inter']">
<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-primary to-primary-dark text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
        role="navigation"
        aria-label="Navigasi Admin">
        <!-- Header -->
        <div class="p-6 border-b border-white border-opacity-20">
            <h2 class="text-2xl font-bold">Admin</h2>
            <p class="text-sm text-white text-opacity-80 mt-1">Panel Administrasi</p>
        </div>

        <!-- Menu -->
        <nav class="py-2">
            <a href="{{ route('dashboard') }}"
            class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
            </a>
            <a href="{{ route('admin.kelola-pengguna') }}"
            class="nav-link {{ request()->routeIs('admin.kelola-pengguna*') ? 'active' : '' }}">
                <i class="fas fa-users mr-3"></i> Manajemen Pengguna
            </a>
            <a href="{{ route('admin.manajemen-lembaga') }}"
            class="nav-link {{ request()->routeIs('admin.manajemen-lembaga*') ? 'active' : '' }}">
                <i class="fas fa-building mr-3"></i> Manajemen Lembaga
            </a>
            <a href="{{ route('admin.kelola-perangkat') }}"
            class="nav-link {{ request()->routeIs('admin.kelola-perangkat*') ? 'active' : '' }}">
                <i class="fas fa-laptop mr-3"></i> Manajemen Device
            </a>
            <a href="{{ route('admin.manajemen-materi') }}"
            class="nav-link {{ request()->routeIs('admin.manajemen-materi*') ? 'active' : '' }}">
                <i class="fas fa-braille mr-3"></i> Manajemen Materi
            </a>
            <a href="{{ route('admin.pengaturan') }}"
            class="nav-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}">
                <i class="fas fa-cogs mr-3"></i> Pengaturan
            </a>
        </nav>
    </aside>

    <!-- Konten Utama -->
    <div class="flex-1 flex flex-col lg:ml-64"> 
        <!-- Topbar -->
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="flex items-center justify-between px-4 py-3 lg:px-8">
                <!-- Tombol Mobile & Logo -->
                <div class="flex items-center space-x-4">
                    <button id="menuToggle"
                            class="lg:hidden p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary"
                            aria-label="Toggle menu"
                            aria-controls="sidebar"
                            aria-expanded="false">
                        <i class="fas fa-bars text-xl text-gray-700"></i>
                    </button>
                    <a href="{{ route('home') }}"
                       class="flex items-center text-primary font-bold text-xl">
                        <i class="fas fa-home mr-2"></i> Inkluvia
                    </a>
                </div>

                <!-- User -->
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                        </div>
                        <div class="hidden md:block">
                            <div class="font-semibold text-gray-900 text-sm">{{ Auth::user()->nama_lengkap }}</div>
                            <div class="text-xs text-gray-500">Administrator</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-200 text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Flash Message -->
        @if(session('success'))
            <div class="mx-4 mt-4 lg:mx-8 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4 lg:mx-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mx-4 mt-4 lg:mx-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mr-3 text-red-600 mt-0.5"></i>
                    <div>
                        <strong class="font-semibold">Terjadi kesalahan:</strong>
                        <ul class="mt-2 ml-4 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Konten -->
        <main class="flex-1 px-4 py-6 lg:px-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4 px-4 lg:px-8 mt-auto text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} Inkluvia - Panel Administrasi
        </footer>
    </div>
</div>

<style>
.nav-link {
    display: block;
    padding: 1rem 1.5rem;
    color: rgba(255, 255, 255, 0.9);
    border-left: 4px solid transparent;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}
.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
}
.nav-link.active {
    background-color: rgba(167,170,255,0.2);
    border-left-color: #A7AAFF;
    font-weight: 500;
    color: #fff;
}
</style>
</body>
</html>