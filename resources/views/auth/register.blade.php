@extends('layouts.app')

@section('title', 'Daftar Akun — KursusApp')

@section('content')
    <div class="min-h-[80vh] grid place-items-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="mb-8 text-center">
                <h1 class="font-display text-3xl tracking-tight text-(--color-text)">Buat akun baru</h1>
                <p class="mt-2 text-(--color-text-muted)">Mulai perjalanan belajar Anda hari ini.</p>
            </div>

            <div class="bg-(--color-surface) rounded-2xl shadow-md border border-(--color-border) p-6 sm:p-8">

                @if ($errors->any())
                    <div role="alert" aria-live="assertive"
                        class="mb-5 p-4 rounded-lg bg-(--color-error-soft) border border-(--color-error)/20 text-(--color-error)">
                        <p class="text-sm font-semibold mb-1">Periksa kembali isian berikut</p>
                        <ul class="text-sm space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>· {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-(--color-text) mb-1.5">
                            Nama lengkap <span class="text-(--color-error)" aria-hidden="true">*</span>
                        </label>
                        <input id="name" name="name" type="text" required autocomplete="name"
                            value="{{ old('name') }}" placeholder="Nama lengkap Anda"
                            class="block w-full h-11 px-3.5 rounded-lg bg-(--color-surface) border border-(--color-border-strong) text-(--color-text) placeholder:text-(--color-text-subtle) focus:border-(--color-brand-600) focus:ring-2 focus:ring-(--color-brand-600)/20 transition">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-(--color-text) mb-1.5">
                            Email <span class="text-(--color-error)" aria-hidden="true">*</span>
                        </label>
                        <input id="email" name="email" type="email" required autocomplete="email"
                            inputmode="email" value="{{ old('email') }}" placeholder="nama@email.com"
                            class="block w-full h-11 px-3.5 rounded-lg bg-(--color-surface) border border-(--color-border-strong) text-(--color-text) placeholder:text-(--color-text-subtle) focus:border-(--color-brand-600) focus:ring-2 focus:ring-(--color-brand-600)/20 transition">
                    </div>

                    {{-- Password --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-(--color-text) mb-1.5">
                            Password <span class="text-(--color-error)" aria-hidden="true">*</span>
                        </label>
                        <div class="relative">
                            <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                autocomplete="new-password" placeholder="Minimal 8 karakter"
                                aria-describedby="password-help"
                                class="block w-full h-11 px-3.5 pr-11 rounded-lg bg-(--color-surface) border border-(--color-border-strong) text-(--color-text) placeholder:text-(--color-text-subtle) focus:border-(--color-brand-600) focus:ring-2 focus:ring-(--color-brand-600)/20 transition">
                            <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 w-11 inline-flex items-center justify-center text-(--color-text-muted) hover:text-(--color-text)"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p id="password-help" class="mt-1 text-xs text-(--color-text-subtle)">
                            Kombinasikan huruf besar, kecil, dan angka untuk keamanan terbaik.
                        </p>
                    </div>

                    {{-- Konfirmasi --}}
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-(--color-text) mb-1.5">
                            Konfirmasi password <span class="text-(--color-error)" aria-hidden="true">*</span>
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            autocomplete="new-password" placeholder="Ulangi password"
                            class="block w-full h-11 px-3.5 rounded-lg bg-(--color-surface) border border-(--color-border-strong) text-(--color-text) placeholder:text-(--color-text-subtle) focus:border-(--color-brand-600) focus:ring-2 focus:ring-(--color-brand-600)/20 transition">
                    </div>

                    <p class="text-xs text-(--color-text-subtle)">
                        Dengan mendaftar, Anda menyetujui
                        <a href="#" class="text-(--color-brand-600) hover:text-(--color-brand-700) underline underline-offset-2">Syarat Layanan</a>
                        dan
                        <a href="#" class="text-(--color-brand-600) hover:text-(--color-brand-700) underline underline-offset-2">Kebijakan Privasi</a>.
                    </p>

                    <button type="submit"
                        class="w-full h-11 inline-flex items-center justify-center rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition">
                        Buat akun
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-(--color-text-muted)">
                Sudah punya akun?
                <a href="{{ route('login') }}"
                    class="font-semibold text-(--color-brand-600) hover:text-(--color-brand-700)">
                    Masuk
                </a>
            </p>
        </div>
    </div>
@endsection
