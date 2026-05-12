<!DOCTYPE html>
<html lang="id" x-data="{
    dark: (localStorage.getItem('theme') ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark'
}" x-init="$watch('dark', v => { localStorage.setItem('theme', v ? 'dark' : 'light');
    document.documentElement.classList.toggle('dark', v) });
document.documentElement.classList.toggle('dark', dark)" :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FAFAF9" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0B1020" media="(prefers-color-scheme: dark)">
    <title>@yield('title', 'KursusApp — Belajar dari mentor profesional')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|instrument-serif:400&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-dvh flex flex-col antialiased">

    {{-- Skip link --}}
    <a href="#main" class="skip-link">Lewati ke konten utama</a>

    {{-- Navbar --}}
    <header class="sticky top-0 z-40 bg-(--color-surface)/85 backdrop-blur border-b border-(--color-border)"
        x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2 font-display text-xl tracking-tight">
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) font-sans font-bold text-sm">K</span>
                <span class="text-(--color-text)">KursusApp</span>
            </a>

            {{-- Primary nav (desktop) --}}
            <nav class="hidden md:flex items-center gap-1" aria-label="Navigasi utama">
                @php $r = request()->path(); @endphp
                <a href="/"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition hover:bg-(--color-surface-2) {{ $r === '/' ? 'text-(--color-brand-600)' : 'text-(--color-text)' }}">
                    Home
                </a>
                <a href="/#kursus"
                    class="px-3 py-2 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">
                    Kursus
                </a>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition hover:bg-(--color-surface-2) {{ str_starts_with($r, 'dashboard') ? 'text-(--color-brand-600)' : 'text-(--color-text)' }}">
                        Dashboard
                    </a>
                    <a href="{{ url('/my-courses') }}"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition hover:bg-(--color-surface-2) {{ str_starts_with($r, 'my-courses') ? 'text-(--color-brand-600)' : 'text-(--color-text)' }}">
                        Kursus Saya
                    </a>
                @endauth
            </nav>

            {{-- Right cluster --}}
            <div class="flex items-center gap-2">

                {{-- Theme toggle --}}
                <button type="button" @click="dark = !dark"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-(--color-text-muted) hover:bg-(--color-surface-2) hover:text-(--color-text) transition"
                    :aria-label="dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'">
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                    <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                </button>

                {{-- Auth area --}}
                @auth
                    <div class="relative hidden sm:block" x-data="{ menu: false }" @click.outside="menu = false">
                        <button type="button" @click="menu = !menu"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-(--color-surface-2) transition"
                            :aria-expanded="menu" aria-haspopup="menu">
                            <span
                                class="w-8 h-8 rounded-full bg-(--color-brand-100) text-(--color-brand-700) flex items-center justify-center font-semibold text-sm">
                                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="text-sm font-medium text-(--color-text) max-w-[10rem] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 text-(--color-text-muted)" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="menu" x-cloak x-transition.opacity.duration.150ms
                            class="absolute right-0 mt-2 w-56 rounded-xl bg-(--color-surface) border border-(--color-border) shadow-lg overflow-hidden"
                            role="menu">
                            <a href="{{ url('/dashboard') }}"
                                class="block px-4 py-2.5 text-sm text-(--color-text) hover:bg-(--color-surface-2)"
                                role="menuitem">Dashboard</a>
                            <a href="{{ url('/my-courses') }}"
                                class="block px-4 py-2.5 text-sm text-(--color-text) hover:bg-(--color-surface-2)"
                                role="menuitem">Kursus Saya</a>
                            <a href="{{ url('/certificates') }}"
                                class="block px-4 py-2.5 text-sm text-(--color-text) hover:bg-(--color-surface-2)"
                                role="menuitem">Sertifikat</a>
                            <div class="h-px bg-(--color-border)"></div>
                            <form action="{{ route('logout') }}" method="POST" role="none">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2.5 text-sm text-(--color-error) hover:bg-(--color-error-soft) transition"
                                    role="menuitem">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2) transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center px-4 h-10 rounded-lg text-sm font-semibold bg-(--color-brand-600) text-(--color-text-on-brand) hover:bg-(--color-brand-700) shadow-sm transition">
                        Daftar
                    </a>
                @endauth

                {{-- Mobile menu trigger --}}
                <button type="button" @click="open = !open"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-(--color-text-muted) hover:bg-(--color-surface-2) hover:text-(--color-text) transition"
                    :aria-expanded="open" aria-label="Buka menu navigasi">
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                    <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile drawer --}}
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms
            class="md:hidden border-t border-(--color-border) bg-(--color-surface)">
            <nav class="px-4 py-3 space-y-1" aria-label="Navigasi mobile">
                <a href="/"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">Home</a>
                <a href="/#kursus"
                    class="block px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">Kursus</a>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">Dashboard</a>
                    <a href="{{ url('/my-courses') }}"
                        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">Kursus
                        Saya</a>
                    <a href="{{ url('/certificates') }}"
                        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">Sertifikat</a>
                    <div class="h-px my-2 bg-(--color-border)"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button
                            class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-error) hover:bg-(--color-error-soft)">
                            Logout
                        </button>
                    </form>
                @else
                    <div class="h-px my-2 bg-(--color-border)"></div>
                    <a href="{{ route('login') }}"
                        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-(--color-text) hover:bg-(--color-surface-2)">Login</a>
                    <a href="{{ route('register') }}"
                        class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-center bg-(--color-brand-600) text-(--color-text-on-brand)">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Flash messages (global) --}}
    @if (session('success') || session('error'))
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4" role="status" aria-live="polite">
            @if (session('success'))
                <div
                    class="flex items-start gap-3 p-4 rounded-xl bg-(--color-success-soft) border border-(--color-success)/20 text-(--color-success)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-5 h-5 mt-0.5 shrink-0" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div
                    class="flex items-start gap-3 p-4 rounded-xl bg-(--color-error-soft) border border-(--color-error)/20 text-(--color-error)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-5 h-5 mt-0.5 shrink-0" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-1-9a.75.75 0 0 0-.75.75v3.5a.75.75 0 0 0 1.5 0v-3.5A.75.75 0 0 0 10 5Z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Main --}}
    <main id="main" class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-16 border-t border-(--color-border) bg-(--color-surface)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid gap-8 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-3">
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) font-bold text-sm">K</span>
                    <span class="font-display text-xl text-(--color-text)">KursusApp</span>
                </div>
                <p class="text-sm text-(--color-text-muted) max-w-sm">
                    Platform belajar online untuk meningkatkan skill profesional Anda bersama mentor terbaik.
                </p>
            </div>
            <div>
                <h6 class="text-xs font-semibold uppercase tracking-wider text-(--color-text-subtle) mb-3">Produk</h6>
                <ul class="space-y-2 text-sm">
                    <li><a href="/#kursus" class="text-(--color-text-muted) hover:text-(--color-brand-600)">Kursus</a>
                    </li>
                    <li><a href="/"
                            class="text-(--color-text-muted) hover:text-(--color-brand-600)">Kategori</a></li>
                    <li><a href="/" class="text-(--color-text-muted) hover:text-(--color-brand-600)">Mentor</a>
                    </li>
                </ul>
            </div>
            <div>
                <h6 class="text-xs font-semibold uppercase tracking-wider text-(--color-text-subtle) mb-3">Perusahaan
                </h6>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="text-(--color-text-muted) hover:text-(--color-brand-600)">Tentang</a>
                    </li>
                    <li><a href="/" class="text-(--color-text-muted) hover:text-(--color-brand-600)">Kontak</a>
                    </li>
                    <li><a href="/"
                            class="text-(--color-text-muted) hover:text-(--color-brand-600)">Kebijakan</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-(--color-border)">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-xs text-(--color-text-subtle) flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <p>© {{ date('Y') }} KursusApp. Hak cipta dilindungi.</p>
                <p>Dibangun dengan Laravel · Tailwind CSS</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
