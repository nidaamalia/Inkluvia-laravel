<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inkluvia adalah platform pendidikan inklusif berbasis web untuk tunanetra, didukung perangkat braille digital buatan Indonesia.">
    <meta name="keywords" content="Inkluvia, pendidikan inklusif, braille digital, tunanetra, belajar mandiri, disabilitas netra, edukasi Indonesia, edubraille">
    <meta name="author" content="Inkluvia Team">
    <meta property="og:title" content="Platform Edukasi Inklusif Digital">
    <meta property="og:description" content="Website belajar aksesibel yang terintegrasi dengan braille display karya anak bangsa. Bantu tunanetra belajar mandiri dari rumah maupun sekolah.">
    <meta property="og:image" content="{{ asset('assets/og.png') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">
    <title>@yield('title', 'Inkluvia – Platform Edukasi Inklusif Indonesia')</title>
    <link rel="icon" href="{{ asset('assets/icon.png') }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="loading">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>
    
    <!-- Enhanced Navbar with Hide/Show -->
    <header class="navbar" role="banner" id="navbar">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">Inkluvia</a>
            
            <nav aria-label="Navigasi utama" class="nav-links">
                <a href="#beranda" class="nav-link active">Beranda</a>
                <a href="#tentang" class="nav-link">Masalah</a>
                <a href="#solusi" class="nav-link">Solusi</a>
                <a href="#kontak" class="nav-link">Kontak</a>
            </nav>

            <button class="mobile-menu-btn" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="auth-buttons">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main id="main-content" role="main">
        <!-- Hero Section -->
        <section class="section hero" id="beranda">
            <div class="section-container">
                <div class="hero-content">
                    <h1>Selamat Datang di <span class="highlight">Inkluvia</span></h1>
                    <p class="subtitle">Platform Edukasi Inklusif Berbasis Web & Braille Digital</p>
                    <p class="desc">Belajar mandiri untuk tunanetra kini lebih mudah dengan kombinasi web app dan perangkat braille display karya anak bangsa.</p>
                    <button class="cta" onclick="scrollToSection('showcase')" aria-label="Mulai belajar di Inkluvia">
                        <i class="fas fa-rocket" style="margin-right: 0.5rem;"></i>
                        Mulai Belajar
                    </button>
                </div>
            </div>
        </section>

        <!-- Problems Section -->
        <section class="section problems" id="tentang">
            <div class="section-container">
                <h2>Kami Hadir Karena Masalah Nyata</h2>
                <div class="cards">
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Pendidikan Inklusif Terbatas</h3>
                        <p>90% penyandang disabilitas netra di Indonesia belum mendapatkan pendidikan inklusif yang layak dan berkualitas.</p>
                    </div>
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3>Perangkat Braille Mahal</h3>
                        <p>Perangkat braille digital impor harganya sangat mahal dan sulit diperbaiki di dalam negeri.</p>
                    </div>
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3>Platform Daring Terbatas</h3>
                        <p>Belum ada platform daring ramah tunanetra yang terjangkau dan mudah digunakan.</p>
                    </div>
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Metode Pembelajaran Konvensional</h3>
                        <p>Metode pembelajaran masih konvensional, guru harus mendikte dan siswa menulis pakai reglet.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solutions Section -->
        <section class="section solutions" id="solusi">
            <div class="section-container-verse2">
                <h2>Solusi dari Inkluvia</h2>
                <div class="cards">
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-universal-access"></i>
                        </div>
                        <h3>Web App Ramah Tunanetra</h3>
                        <p>Web app yang bisa diakses dengan pembaca layar dan keyboard-only navigation yang intuitif.</p>
                    </div>
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-link"></i>
                        </div>
                        <h3>Integrasi EduBraille</h3>
                        <p>Integrasi langsung dengan EduBraille – refreshable braille display buatan lokal yang terjangkau.</p>
                    </div>
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Pembelajaran Mandiri</h3>
                        <p>Kontrol suara dan sistem yang mendukung pembelajaran mandiri yang efektif dan interaktif.</p>
                    </div>
                    <div class="card animate-on-scroll">
                        <div class="card-icon">
                            <i class="fas fa-magic"></i>
                        </div>
                        <h3>Konversi Instan</h3>
                        <p>Fitur konversi materi belajar ke braille secara instan dan mudah dengan teknologi AI.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Community Section -->
        <section class="section community" id="kontak">
            <div class="section-container">
                <h2>Dibangun Bersama Komunitas</h2>
                <p class="desc animate-on-scroll">Inkluvia bekerja sama dengan Refreshable Braille lokal yang telah dikembangkan sejak 2015 bersama guru SLB, relawan, dan komunitas netra. Bukan sekadar teknologi untuk tunanetra, tapi solusi yang dirancang bersama mereka.</p>
                <div class="stats">
                    <div class="stat animate-on-scroll">
                        <span class="number">2015</span>
                        <span class="label">Tahun Pengembangan</span>
                    </div>
                    <div class="stat animate-on-scroll">
                        <span class="number">5+</span>
                        <span class="label">Penghargaan Nasional & ASEAN</span>
                    </div>
                    <div class="stat animate-on-scroll">
                        <span class="number">100+</span>
                        <span class="label">Komunitas & SLB Partner</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Enhanced Footer -->
    <footer role="contentinfo" class="footer">
        <div class="footer-content">
            <p>&copy; 2015–{{ date('Y') }} Inkluvia. Hak cipta dilindungi undang-undang.</p>
            <p>Inkluvia adalah platform pendidikan inklusif untuk tunanetra, dibangun bersama komunitas dan pendidik di Indonesia.</p>
            <a href="https://wa.me/6285749797955" target="_blank" rel="noopener" class="btn">
                <i class="fab fa-whatsapp"></i> Hubungi Kami
            </a>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>