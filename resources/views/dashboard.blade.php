@extends('layouts.app')

@section('title', 'Dashboard — KursusApp')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">

        {{-- Welcome strip --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">Dashboard</span>
                <h1 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">
                    Selamat datang, {{ explode(' ', auth()->user()->name)[0] ?? 'Siswa' }}
                </h1>
                <p class="mt-2 text-(--color-text-muted)">Ringkasan aktivitas belajar Anda.</p>
            </div>
            <a href="{{ url('/my-courses') }}"
                class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) transition shadow-sm">
                Lanjutkan belajar
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    class="w-4 h-4 ml-2" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>

        {{-- KPI cards --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">

            {{-- Kursus --}}
            <article class="p-6 rounded-xl bg-(--color-surface) border border-(--color-border) shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-(--color-text-muted)">Kursus Aktif</p>
                        <p class="mt-2 font-display text-4xl tracking-tight text-(--color-text) tabular-nums">
                            {{ $coursesCount }}
                        </p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-(--color-brand-100) text-(--color-brand-700)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </span>
                </div>
                <a href="{{ url('/my-courses') }}"
                    class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-(--color-brand-600) hover:text-(--color-brand-700)">
                    Lihat semua
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-3.5 h-3.5" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </article>

            {{-- Sertifikat --}}
            <article class="p-6 rounded-xl bg-(--color-surface) border border-(--color-border) shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-(--color-text-muted)">Sertifikat</p>
                        <p class="mt-2 font-display text-4xl tracking-tight text-(--color-text) tabular-nums">
                            {{ $certificatesCount }}
                        </p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-(--color-accent-100) text-(--color-accent-600)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 18.75h-9a9.7 9.7 0 0 1-.412-.011c-1.1-.05-1.838-.834-1.838-1.989V7.5c0-1.155.738-1.939 1.838-1.989a9.7 9.7 0 0 1 .412-.011h9a9.7 9.7 0 0 1 .412.011c1.1.05 1.838.834 1.838 1.989v9.25c0 1.155-.738 1.939-1.838 1.989a9.7 9.7 0 0 1-.412.011Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 10.5 14.25 15 9.75" />
                        </svg>
                    </span>
                </div>
                <a href="{{ url('/certificates') }}"
                    class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-(--color-brand-600) hover:text-(--color-brand-700)">
                    Kelola sertifikat
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-3.5 h-3.5" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </article>

            {{-- Status --}}
            <article class="p-6 rounded-xl bg-(--color-surface) border border-(--color-border) shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-(--color-text-muted)">Status Akun</p>
                        <p class="mt-2 inline-flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-(--color-success)"></span>
                            <span class="font-display text-2xl tracking-tight text-(--color-text)">Aktif</span>
                        </p>
                    </div>
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-(--color-success-soft) text-(--color-success)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm text-(--color-text-muted)">
                    Akun Anda dalam kondisi baik.
                </p>
            </article>
        </div>

        {{-- Quick links --}}
        <h2 class="mt-12 mb-4 font-display text-2xl tracking-tight text-(--color-text)">
            Akses cepat
        </h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ url('/my-courses') }}"
                class="group flex items-center gap-4 p-5 rounded-xl bg-(--color-surface) border border-(--color-border) hover:border-(--color-brand-500) hover:shadow-md transition">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-(--color-brand-50) text-(--color-brand-700)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-semibold text-(--color-text)">Kursus Saya</p>
                    <p class="text-sm text-(--color-text-muted)">Lihat semua kursus terdaftar</p>
                </div>
            </a>

            <a href="{{ url('/certificates') }}"
                class="group flex items-center gap-4 p-5 rounded-xl bg-(--color-surface) border border-(--color-border) hover:border-(--color-brand-500) hover:shadow-md transition">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-(--color-accent-100) text-(--color-accent-600)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 18.75h-9a9.7 9.7 0 0 1-.412-.011c-1.1-.05-1.838-.834-1.838-1.989V7.5c0-1.155.738-1.939 1.838-1.989a9.7 9.7 0 0 1 .412-.011h9a9.7 9.7 0 0 1 .412.011c1.1.05 1.838.834 1.838 1.989v9.25c0 1.155-.738 1.939-1.838 1.989a9.7 9.7 0 0 1-.412.011Z" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-semibold text-(--color-text)">Sertifikat</p>
                    <p class="text-sm text-(--color-text-muted)">Unduh sertifikat yang sudah terbit</p>
                </div>
            </a>

            <a href="/#kursus"
                class="group flex items-center gap-4 p-5 rounded-xl bg-(--color-surface) border border-(--color-border) hover:border-(--color-brand-500) hover:shadow-md transition">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-(--color-info-soft) text-(--color-info)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="font-semibold text-(--color-text)">Jelajahi Kursus</p>
                    <p class="text-sm text-(--color-text-muted)">Temukan kursus baru</p>
                </div>
            </a>
        </div>
    </div>
@endsection
