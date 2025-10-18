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
            <a href="{{ route('home') }}" class="logo" aria-label="Inkluvia - Kembali ke beranda">Inkluvia</a>
            
            <nav aria-label="Navigasi utama" class="nav-links">
                <a href="#beranda" class="nav-link active" aria-current="page">Beranda</a>
                <a href="#tentang" class="nav-link">Masalah</a>
                <a href="#solusi" class="nav-link">Solusi</a>
                <a href="#kontak" class="nav-link">Kontak</a>
            </nav>

            <button class="mobile-menu-btn" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobile-menu">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            
            <div class="auth-buttons" role="navigation" aria-label="Navigasi autentikasi">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> Register
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main id="main-content" role="main">
        <!-- Hero Section -->
        <section class="section hero" id="beranda" aria-labelledby="hero-heading">
            <div class="section-container">
                <div class="hero-content">
                    <h1 id="hero-heading">Selamat Datang di <span class="highlight">Inkluvia</span></h1>
                    <p class="subtitle">Platform Edukasi Inklusif Berbasis Web & Braille Digital</p>
                    <p class="desc">Belajar mandiri untuk tunanetra kini lebih mudah dengan kombinasi web app dan perangkat braille display karya anak bangsa.</p>
                    <div role="group" aria-label="Tombol aksi utama">
                        <button class="cta" onclick="scrollToSection('showcase')" aria-label="Mulai belajar di Inkluvia">
                            <i class="fas fa-rocket" style="margin-right: 0.5rem;" aria-hidden="true"></i>
                            Mulai Belajar
                        </button>
                        <button class="cta" onclick="openInstitutionForm()" aria-label="Daftarkan lembaga Anda">
                            <i class="fas fa-building" style="margin-right: 0.5rem;" aria-hidden="true"></i>
                            Daftar Lembaga
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Problems Section -->
        <section class="section problems" id="tentang" aria-labelledby="problems-heading">
            <div class="section-container">
                <h2 id="problems-heading">Kami Hadir Karena Masalah Nyata</h2>
                <div class="cards" role="list">
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Pendidikan Inklusif Terbatas</h3>
                        <p>90% penyandang disabilitas netra di Indonesia belum mendapatkan pendidikan inklusif yang layak dan berkualitas.</p>
                    </article>
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3>Perangkat Braille Mahal</h3>
                        <p>Perangkat braille digital impor harganya sangat mahal dan sulit diperbaiki di dalam negeri.</p>
                    </article>
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3>Platform Daring Terbatas</h3>
                        <p>Belum ada platform daring ramah tunanetra yang terjangkau dan mudah digunakan.</p>
                    </article>
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h3>Metode Pembelajaran Konvensional</h3>
                        <p>Metode pembelajaran masih konvensional, guru harus mendikte dan siswa menulis pakai reglet.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Solutions Section -->
        <section class="section solutions" id="solusi" aria-labelledby="solutions-heading">
            <div class="section-container-verse2">
                <h2 id="solutions-heading">Solusi dari Inkluvia</h2>
                <div class="cards" role="list">
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-universal-access"></i>
                        </div>
                        <h3>Web App Ramah Tunanetra</h3>
                        <p>Web app yang bisa diakses dengan pembaca layar dan keyboard-only navigation yang intuitif.</p>
                    </article>
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-link"></i>
                        </div>
                        <h3>Integrasi EduBraille</h3>
                        <p>Integrasi langsung dengan EduBraille – refreshable braille display buatan lokal yang terjangkau.</p>
                    </article>
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Pembelajaran Mandiri</h3>
                        <p>Kontrol suara dan sistem yang mendukung pembelajaran mandiri yang efektif dan interaktif.</p>
                    </article>
                    <article class="card animate-on-scroll" role="listitem">
                        <div class="card-icon" aria-hidden="true">
                            <i class="fas fa-magic"></i>
                        </div>
                        <h3>Konversi Instan</h3>
                        <p>Fitur konversi materi belajar ke braille secara instan dan mudah dengan teknologi AI.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Community Section -->
        <section class="section community" id="kontak" aria-labelledby="community-heading">
            <div class="section-container">
                <h2 id="community-heading">Dibangun Bersama Komunitas</h2>
                <p class="desc animate-on-scroll">Inkluvia bekerja sama dengan Refreshable Braille lokal yang telah dikembangkan sejak 2015 bersama guru SLB, relawan, dan komunitas netra. Bukan sekadar teknologi untuk tunanetra, tapi solusi yang dirancang bersama mereka.</p>
                <div class="stats" role="list" aria-label="Statistik Inkluvia">
                    <div class="stat animate-on-scroll" role="listitem">
                        <span class="number" aria-label="Tahun 2015">2015</span>
                        <span class="label">Tahun Pengembangan</span>
                    </div>
                    <div class="stat animate-on-scroll" role="listitem">
                        <span class="number" aria-label="Lebih dari 5">5+</span>
                        <span class="label">Penghargaan Nasional & ASEAN</span>
                    </div>
                    <div class="stat animate-on-scroll" role="listitem">
                        <span class="number" aria-label="Lebih dari 100">100+</span>
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
            <a href="https://wa.me/6285749797955" target="_blank" rel="noopener noreferrer" class="btn" aria-label="Hubungi kami melalui WhatsApp (buka di tab baru)">
                <i class="fab fa-whatsapp" aria-hidden="true"></i> Hubungi Kami
            </a>
        </div>
    </footer>

    <!-- Live Region for Announcements -->
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function openInstitutionForm() {
            const modal = document.getElementById('institution-modal');
            modal.style.display = 'block';
            modal.setAttribute('aria-hidden', 'false');
            
            // Focus on first input
            setTimeout(() => {
                const firstInput = modal.querySelector('input[name="name"]');
                if (firstInput) firstInput.focus();
            }, 100);
            
            // Trap focus in modal
            document.body.style.overflow = 'hidden';
            
            document.getElementById('announcements').textContent = 'Dialog pendaftaran lembaga dibuka';
        }

        function closeInstitutionForm() {
            const modal = document.getElementById('institution-modal');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            
            // Return focus to button
            const trigger = document.querySelector('button[onclick="openInstitutionForm()"]');
            if (trigger) trigger.focus();
            
            document.getElementById('announcements').textContent = 'Dialog pendaftaran lembaga ditutup';
        }

        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
                section.focus();
            }
        }

        // Keyboard accessibility for modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('institution-modal');
            
            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'block') {
                    closeInstitutionForm();
                }
            });
            
            // Close on backdrop click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeInstitutionForm();
                }
            });
        });
    </script>

    <!-- Simple Modal Form -->
    <div id="institution-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="institution-modal-title" aria-hidden="true" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:1000;">
        <div class="modal-content" style="max-width:640px; margin:5% auto; background:#fff; padding:1.5rem; border-radius:.75rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                <h2 id="institution-modal-title" style="margin:0; font-size:1.5rem;">Daftar Lembaga</h2>
                <button onclick="closeInstitutionForm()" aria-label="Tutup dialog" style="background:none; border:none; font-size:1.5rem; cursor:pointer; padding:0.5rem; line-height:1;">×</button>
            </div>
            @if(session('status'))
                <div role="status" aria-live="polite" style="margin-bottom:1rem; padding:.75rem; background:#e8f7ee; border:1px solid #b6e2c5; border-radius:.5rem; color:#146c43;">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('institution.register') }}" style="display:grid; gap:.75rem;">
                @csrf
                <label for="institution-name">
                    <span>Nama <span aria-label="wajib diisi">*</span></span>
                    <input id="institution-name" name="name" type="text" value="{{ old('name') }}" required aria-required="true" style="width:100%; padding:.6rem; border:1px solid #ccc; border-radius:.5rem; margin-top:0.25rem;">
                </label>
                <label for="institution-email">
                    <span>Email <span aria-label="wajib diisi">*</span></span>
                    <input id="institution-email" name="email" type="email" value="{{ old('email') }}" required aria-required="true" style="width:100%; padding:.6rem; border:1px solid #ccc; border-radius:.5rem; margin-top:0.25rem;">
                </label>
                <label for="institution-type">
                    <span>Tipe <span aria-label="wajib diisi">*</span></span>
                    <select id="institution-type" name="type" required aria-required="true" style="width:100%; padding:.6rem; border:1px solid #ccc; border-radius:.5rem; margin-top:0.25rem;">
                        <option value="">Pilih tipe</option>
                        <option value="Sekolah" @selected(old('type')==='Sekolah')>Sekolah</option>
                        <option value="Lembaga" @selected(old('type')==='Lembaga')>Lembaga</option>
                    </select>
                </label>
                <label for="institution-institution-name">
                    <span>Nama Lembaga <span aria-label="wajib diisi">*</span></span>
                    <input id="institution-institution-name" name="institution_name" type="text" value="{{ old('institution_name') }}" required aria-required="true" style="width:100%; padding:.6rem; border:1px solid #ccc; border-radius:.5rem; margin-top:0.25rem;">
                </label>
                <label for="institution-address">
                    <span>Alamat <span aria-label="wajib diisi">*</span></span>
                    <input id="institution-address" name="address" type="text" value="{{ old('address') }}" required aria-required="true" style="width:100%; padding:.6rem; border:1px solid #ccc; border-radius:.5rem; margin-top:0.25rem;">
                </label>
                <label for="institution-description">
                    <span>Deskripsi (opsional)</span>
                    <textarea id="institution-description" name="description" rows="4" style="width:100%; padding:.6rem; border:1px solid #ccc; border-radius:.5rem; margin-top:0.25rem;">{{ old('description') }}</textarea>
                </label>

                @if ($errors->any())
                    <div role="alert" aria-live="assertive" style="padding:.75rem; background:#fff4e5; border:1px solid #ffd8a8; border-radius:.5rem; color:#995c00;">
                        <p style="font-weight:600; margin-bottom:0.5rem;">Terdapat kesalahan pada formulir:</p>
                        <ul style="margin:0; padding-left:1.5rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:.25rem;">
                    <button type="button" class="btn btn-outline" onclick="closeInstitutionForm()">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>

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
    </style>

</body>

</html>