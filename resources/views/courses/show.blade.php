@extends('layouts.app')

@section('title', $course->title . ' — KursusApp')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Breadcrumb --}}
        <nav class="mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-(--color-text-muted)">
                <li><a href="/" class="hover:text-(--color-brand-600)">Home</a></li>
                <li aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4">
                        <path fill-rule="evenodd"
                            d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </li>
                <li><a href="/#kursus" class="hover:text-(--color-brand-600)">Kursus</a></li>
                <li aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4">
                        <path fill-rule="evenodd"
                            d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </li>
                <li class="text-(--color-text) font-medium truncate max-w-[12rem]" aria-current="page">
                    {{ $course->title }}
                </li>
            </ol>
        </nav>

        @auth
            @php
                $registration = auth()->user()->registrations->where('course_id', $course->id)->first();
            @endphp
        @endauth

        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Main column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Hero card --}}
                <article class="bg-(--color-surface) rounded-2xl border border-(--color-border) shadow-sm overflow-hidden">
                    <div class="relative aspect-[16/9] bg-(--color-surface-2) overflow-hidden">
                        <div aria-hidden="true"
                            class="absolute inset-0 bg-gradient-to-br from-(--color-brand-200) via-(--color-brand-100) to-(--color-accent-100)">
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="font-display text-7xl text-(--color-brand-700)/40">
                                {{ strtoupper(mb_substr($course->title, 0, 1)) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <span class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">
                            Kursus
                        </span>
                        <h1 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">
                            {{ $course->title }}
                        </h1>

                        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-(--color-text-muted)">
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-(--color-accent-500)" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.007Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-(--color-text) font-medium">4.8</span>
                                <span>(120 ulasan)</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                350+ siswa
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Akses seumur hidup
                            </span>
                        </div>

                        <hr class="my-6 border-(--color-border)">

                        <h2 class="font-semibold text-(--color-text) mb-2">Tentang kursus ini</h2>
                        <p class="text-(--color-text-muted) leading-relaxed whitespace-pre-line">
                            {{ $course->description }}
                        </p>
                    </div>
                </article>

                {{-- Apa yang akan dipelajari --}}
                <article class="bg-(--color-surface) rounded-2xl border border-(--color-border) shadow-sm p-6 sm:p-8">
                    <h2 class="font-display text-2xl tracking-tight text-(--color-text)">Yang akan Anda pelajari</h2>
                    <ul class="mt-5 grid sm:grid-cols-2 gap-3" role="list">
                        @foreach (['Konsep dasar dan praktik terbaik', 'Studi kasus dunia nyata', 'Proyek akhir untuk portofolio', 'Sertifikat resmi setelah selesai'] as $item)
                            <li class="flex items-start gap-2.5">
                                <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-(--color-success-soft) text-(--color-success) shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-3.5 h-3.5" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="text-sm text-(--color-text)">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>

                {{-- Upload bukti pembayaran (kondisional) --}}
                @auth
                    @if ($registration && $registration->status == 'pending' && $registration->payment && $registration->payment->status != 'approved')
                        <article class="bg-(--color-surface) rounded-2xl border border-(--color-border) shadow-sm p-6 sm:p-8">
                            <div class="flex items-start gap-3 p-4 mb-5 rounded-lg bg-(--color-warning-soft) text-(--color-warning)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-5 h-5 mt-0.5 shrink-0" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm font-medium">Unggah bukti pembayaran Anda untuk melanjutkan.</p>
                            </div>

                            <h2 class="font-semibold text-(--color-text) mb-3">Unggah bukti pembayaran</h2>

                            <form method="POST" action="{{ route('payment.upload', $registration->id) }}"
                                enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="payment_proof" class="block text-sm font-medium text-(--color-text) mb-1.5">
                                        File bukti pembayaran <span class="text-(--color-error)" aria-hidden="true">*</span>
                                    </label>
                                    <input id="payment_proof" name="payment_proof" type="file" required
                                        accept="image/png,image/jpeg,application/pdf"
                                        class="block w-full text-sm text-(--color-text) file:mr-4 file:h-10 file:px-4 file:rounded-lg file:border-0 file:bg-(--color-brand-50) file:text-(--color-brand-700) file:font-semibold hover:file:bg-(--color-brand-100) file:cursor-pointer cursor-pointer">
                                    <p class="mt-1 text-xs text-(--color-text-subtle)">Format: PNG, JPG, atau PDF. Maks 2 MB.</p>
                                    @error('payment_proof')
                                        <p role="alert" class="mt-1.5 text-sm text-(--color-error)">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-(--color-success) text-white text-sm font-semibold hover:opacity-90 shadow-sm transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                    Unggah bukti
                                </button>
                            </form>
                        </article>
                    @endif
                @endauth
            </div>

            {{-- Sticky purchase card --}}
            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-4">
                    <div class="bg-(--color-surface) rounded-2xl border border-(--color-border) shadow-md p-6">

                        <p class="text-xs uppercase tracking-wider font-semibold text-(--color-text-subtle)">Harga kursus</p>
                        <p class="mt-1 font-display text-4xl tracking-tight text-(--color-text) tabular-nums">
                            Rp {{ number_format($course->price, 0, ',', '.') }}
                        </p>

                        @auth
                            @if (!$registration)
                                <form method="POST" action="{{ route('courses.register', $course->id) }}" class="mt-5">
                                    @csrf
                                    <button type="submit"
                                        class="w-full h-12 inline-flex items-center justify-center rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition">
                                        Daftar Sekarang
                                    </button>
                                </form>
                            @else
                                <div class="mt-5 flex items-center gap-3 p-3 rounded-lg bg-(--color-success-soft) text-(--color-success)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-5 h-5 shrink-0" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm font-semibold">Anda sudah terdaftar</p>
                                </div>
                                <a href="{{ url('/my-courses') }}"
                                    class="mt-3 w-full h-11 inline-flex items-center justify-center rounded-lg bg-(--color-surface-2) text-(--color-text) text-sm font-semibold border border-(--color-border-strong) hover:bg-(--color-surface-3) transition">
                                    Lihat di Kursus Saya
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="mt-5 w-full h-12 inline-flex items-center justify-center rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition">
                                Masuk untuk mendaftar
                            </a>
                            <p class="mt-3 text-xs text-center text-(--color-text-subtle)">
                                Belum punya akun?
                                <a href="{{ route('register') }}" class="text-(--color-brand-600) hover:text-(--color-brand-700) font-semibold">Daftar gratis</a>
                            </p>
                        @endauth

                        <hr class="my-5 border-(--color-border)">

                        <ul class="space-y-2.5 text-sm" role="list">
                            <li class="flex items-center gap-2.5 text-(--color-text)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-(--color-success)" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Sertifikat resmi
                            </li>
                            <li class="flex items-center gap-2.5 text-(--color-text)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-(--color-success)" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Akses materi seumur hidup
                            </li>
                            <li class="flex items-center gap-2.5 text-(--color-text)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-(--color-success)" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Dukungan mentor
                            </li>
                            <li class="flex items-center gap-2.5 text-(--color-text)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-(--color-success)" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Pembayaran aman
                            </li>
                        </ul>
                    </div>

                    {{-- Trust badge --}}
                    <div class="text-center text-xs text-(--color-text-subtle)">
                        <p>🔒 Transaksi diproses secara aman oleh Midtrans</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
