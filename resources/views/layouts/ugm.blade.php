<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'KKNHub - Universitas Gadjah Mada')</title>

    <!-- Google Fonts: Plus Jakarta Sans (Headings/Display) & Inter (Body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ugm: {
                            navy: '#083d62',
                            dark: '#052942',
                            accent: '#185a9d',
                            yellow: '#f3b233',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col font-sans">
    <!-- Header Resmi Universitas Gadjah Mada (Presisi #083d62) -->
    <header class="bg-[#083d62] text-white sticky top-0 z-30 shadow-md border-b border-[#052942]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 xl:px-8 py-2.5">
            <div class="flex items-center justify-between gap-2 lg:gap-4 xl:gap-6">
                <!-- Sisi Kiri: Logo & Identitas Resmi UGM KKNHub -->
                <div class="flex items-center gap-3.5 flex-shrink-0">
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 sm:gap-3 group" title="Universitas Gadjah Mada - KKNHub">
                        <img src="/images/ugm-brand-header.png" alt="Universitas Gadjah Mada" class="h-10 sm:h-12 xl:h-13 w-auto object-contain">
                        <div class="h-6 sm:h-7 w-px bg-white/20 hidden md:block"></div>
                        <div class="hidden md:flex flex-col justify-center">
                            <span class="font-heading font-bold text-xs sm:text-sm xl:text-base tracking-wide text-white leading-tight">KKNHub</span>
                            <span class="text-[9px] text-white/70 font-sans tracking-wider uppercase hidden lg:block">Pusat OPREC Tim KKN</span>
                        </div>
                    </a>
                </div>

                <!-- Bagian Tengah: Menu Navigasi Utama Terdistribusi Proporsional di Sumbu Tengah -->
                <nav class="hidden md:flex items-center justify-center gap-x-2.5 lg:gap-x-4 xl:gap-x-6 text-xs xl:text-sm font-sans font-medium text-white px-1">
                    <a href="{{ route('catalog.index') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('catalog.*') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                        Katalog OPREC
                    </a>

                    @auth
                        {{-- Menu Khusus Mahasiswa --}}
                        @if(auth()->user()->isMahasiswa())
                            <a href="{{ route('applications.history') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('applications.history') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Lamaran Saya
                            </a>
                            <a href="{{ route('pic.register') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('pic.register') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Daftar PIC Tim
                            </a>
                        @endif

                        {{-- Menu Khusus PIC Tim KKN --}}
                        @if(auth()->user()->isPic())
                            <a href="{{ route('pic.dashboard') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('pic.dashboard') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Panel PIC Tim
                            </a>
                            <a href="{{ route('pic.catalogs.index') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('pic.catalogs.*') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Kelola OPREC
                            </a>
                            <a href="{{ route('pic.team.edit') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('pic.team.*') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Profil Tim
                            </a>
                        @endif

                        {{-- Menu Khusus Administrator --}}
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('admin.dashboard') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Panel Admin
                            </a>
                            <a href="{{ route('admin.pic-registrations.index') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('admin.pic-registrations.*') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Verifikasi PIC
                            </a>
                            <a href="{{ route('admin.catalogs.index') }}" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 {{ request()->routeIs('admin.catalogs.*') ? 'font-semibold text-amber-300 border-b-2 border-amber-300' : 'text-white/90' }}">
                                Moderasi Katalog
                            </a>
                        @endif
                    @else
                        <a href="{{ route('catalog.index') }}#panduan" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 text-white/90">
                            Panduan Rekrutmen
                        </a>
                        <a href="#kontak-kkn" class="whitespace-nowrap hover:text-amber-300 transition-colors py-1 text-white/90">
                            Narahubung
                        </a>
                    @endauth
                </nav>

                <!-- Sisi Kanan: Baris Ganda (Utilitas Media Sosial di Atas & Status Akun di Bawah) -->
                <div class="flex flex-col items-end justify-center gap-1.5 flex-shrink-0">
                    <!-- Baris Atas: Ikon Media Sosial (Desktop Lebar), Bendera ID, dan Ikon Pencarian -->
                    <div class="hidden sm:flex items-center gap-3 text-white/90">
                        <!-- Media Sosial Resmi UGM (Hanya tampil di layar lebar agar tidak sesak di laptop standar) -->
                        <div class="hidden xl:flex items-center gap-3">
                            <!-- Instagram -->
                            <a href="https://www.instagram.com/ugm.yogyakarta" target="_blank" rel="noopener noreferrer" class="hover:text-white transition" title="Instagram">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <!-- YouTube -->
                            <a href="https://www.youtube.com/@UGMOfficial" target="_blank" rel="noopener noreferrer" class="hover:text-white transition" title="YouTube">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                            <!-- Facebook -->
                            <a href="https://www.facebook.com/UGM.Yogyakarta" target="_blank" rel="noopener noreferrer" class="hover:text-white transition" title="Facebook">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <!-- X / Twitter -->
                            <a href="https://twitter.com/UGMYogyakarta" target="_blank" rel="noopener noreferrer" class="hover:text-white transition" title="X">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/school/universitas-gadjah-mada" target="_blank" rel="noopener noreferrer" class="hover:text-white transition" title="LinkedIn">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                            <!-- TikTok -->
                            <a href="https://www.tiktok.com/@ugm.id" target="_blank" rel="noopener noreferrer" class="hover:text-white transition" title="TikTok">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31 0 2.591.24 3.78.71 0 1.94.97 3.73 2.57 4.79 1.25.83 2.73 1.3 4.28 1.34v4.06c-1.5-.04-2.97-.43-4.28-1.15-.36-.2-.71-.43-1.03-.68v8.66c0 4.19-3.4 7.59-7.59 7.59-4.19 0-7.59-3.4-7.59-7.59 0-4.08 3.23-7.4 7.28-7.57v4.06c-1.83.17-3.22 1.7-3.22 3.51 0 1.95 1.58 3.53 3.53 3.53s3.53-1.58 3.53-3.53V0h1.02z"/></svg>
                            </a>

                            <div class="h-3.5 w-[1px] bg-white/30 mx-0.5"></div>
                        </div>

                        <!-- Bendera Bulat Indonesia & ID -->
                        <div class="flex items-center gap-1.5 text-white">
                            <span class="w-3.5 h-3.5 rounded-full overflow-hidden inline-block border border-white/40 flex-shrink-0 shadow-sm">
                                <span class="block h-1/2 bg-[#ff0000]"></span>
                                <span class="block h-1/2 bg-white"></span>
                            </span>
                            <span class="font-bold tracking-wider text-[11px]">ID</span>
                            <svg class="w-2.5 h-2.5 fill-current opacity-80" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </div>

                        <!-- Ikon Pencarian -->
                        <a href="{{ route('catalog.index') }}" class="hover:text-white transition ml-1" title="Cari Katalog">
                            <svg class="w-3.5 h-3.5 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </a>
                    </div>

                    <!-- Baris Bawah: Status Pengguna / Tombol Masuk yang Rapi & Seimbang -->
                    <div class="flex items-center gap-2 text-xs">
                        @auth
                            <div class="flex items-center gap-1.5 text-white/95 font-sans">
                                <span class="text-white/90 text-xs hidden sm:inline font-sans truncate max-w-[90px] xl:max-w-none">
                                    {{ auth()->user()->name }}
                                </span>
                                @if(auth()->user()->isAdmin())
                                    <span class="bg-amber-400 text-slate-900 font-heading font-bold px-1.5 py-0.5 rounded text-[10px] uppercase tracking-wide">
                                        Admin
                                    </span>
                                @elseif(auth()->user()->isPic())
                                    <span class="bg-sky-400 text-slate-900 font-heading font-bold px-1.5 py-0.5 rounded text-[10px] uppercase tracking-wide">
                                        PIC
                                    </span>
                                @else
                                    <span class="bg-emerald-400 text-slate-900 font-heading font-bold px-1.5 py-0.5 rounded text-[10px] uppercase tracking-wide">
                                        Mhs
                                    </span>
                                @endif
                            </div>
                            <div class="h-3 w-px bg-white/20"></div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-rose-200 hover:text-white transition text-xs font-medium">
                                    Keluar
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-medium px-3 py-1 rounded text-xs transition">
                                <svg class="w-3 h-3 fill-none stroke-current stroke-2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                <span>Masuk Akun UGM</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Tombol Mobile Menu Hamburger (Layar Kecil) -->
                <button type="button" onclick="document.getElementById('mobile-nav-ugm').classList.toggle('hidden')" class="md:hidden text-white/90 hover:text-white p-1 rounded focus:outline-none" title="Buka Navigasi">
                    <svg class="w-6 h-6 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Panel Navigasi Mobile -->
        <div id="mobile-nav-ugm" class="hidden md:hidden border-t border-white/10 bg-[#062f4c] px-4 py-3 space-y-2 text-sm font-sans">
            <a href="{{ route('catalog.index') }}" class="block py-1.5 text-white hover:text-amber-300 {{ request()->routeIs('catalog.*') ? 'font-semibold text-amber-300' : '' }}">
                Katalog OPREC
            </a>
            @auth
                @if(auth()->user()->isMahasiswa())
                    <a href="{{ route('applications.history') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Lamaran Saya
                    </a>
                    <a href="{{ route('pic.register') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Daftar PIC Tim
                    </a>
                @endif
                @if(auth()->user()->isPic())
                    <a href="{{ route('pic.dashboard') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Panel PIC Tim
                    </a>
                    <a href="{{ route('pic.catalogs.index') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Kelola OPREC
                    </a>
                    <a href="{{ route('pic.team.edit') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Profil Tim
                    </a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Panel Admin
                    </a>
                    <a href="{{ route('admin.pic-registrations.index') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Verifikasi PIC
                    </a>
                    <a href="{{ route('admin.catalogs.index') }}" class="block py-1.5 text-white hover:text-amber-300">
                        Moderasi Katalog
                    </a>
                @endif
                <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs text-white/80">
                    <span>{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-rose-300 hover:text-white">Keluar</button>
                    </form>
                </div>
            @else
                <a href="{{ route('catalog.index') }}#panduan" class="block py-1.5 text-white hover:text-amber-300">
                    Panduan Rekrutmen
                </a>
                <a href="#kontak-kkn" class="block py-1.5 text-white hover:text-amber-300">
                    Narahubung
                </a>
                <a href="{{ route('login') }}" class="block py-1.5 text-amber-300 font-semibold">
                    Masuk Akun UGM
                </a>
            @endauth
        </div>
    </header>

    <!-- Konten Utama Halaman -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Resmi UGM -->
    <footer class="bg-[#052942] text-slate-400 text-xs py-8 border-t border-white/10 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="/images/ugm-logo-white.svg" alt="Universitas Gadjah Mada" class="h-9 w-auto object-contain opacity-90">
                <div class="border-l border-white/20 pl-3">
                    <div class="font-heading font-bold text-white text-sm">Direktorat Pengabdian kepada Masyarakat</div>
                    <div class="text-[11px] text-slate-400">Universitas Gadjah Mada - Bulaksumur, D.I. Yogyakarta 55281</div>
                </div>
            </div>
            <div class="text-center md:text-right text-[11px] text-slate-400 font-sans">
                KKNHub v1.1 - Platform Rekrutmen Terbuka KKN UGM. Seluruh hak cipta dilindungi undang-undang.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
