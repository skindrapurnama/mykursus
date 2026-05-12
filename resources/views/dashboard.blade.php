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

        {{-- ============== LANJUTKAN BELAJAR ============== --}}
        <section class="mt-12">
            <div class="flex items-end justify-between gap-3 mb-4">
                <h2 class="font-display text-2xl tracking-tight text-(--color-text)">Lanjutkan belajar</h2>
                <a href="{{ url('/my-courses') }}"
                    class="text-sm font-medium text-(--color-brand-600) hover:text-(--color-brand-700) shrink-0">
                    Lihat semua →
                </a>
            </div>

            @if ($continueLearning->isEmpty())
                <div class="rounded-xl border border-dashed border-(--color-border-strong) bg-(--color-surface) p-8 text-center">
                    <p class="text-(--color-text-muted)">Belum ada kursus aktif untuk dilanjutkan.</p>
                    <a href="/#kursus"
                        class="mt-3 inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) transition">
                        Jelajahi kursus
                    </a>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($continueLearning as $reg)
                        <a href="{{ route('courses.show', $reg->course_id) }}"
                            class="block p-5 rounded-xl bg-(--color-surface) border border-(--color-border) hover:border-(--color-brand-500) hover:shadow-md transition">
                            <span class="text-xs uppercase tracking-wider font-semibold text-(--color-brand-600)">Kursus aktif</span>
                            <h3 class="mt-2 font-semibold text-(--color-text) line-clamp-2 leading-snug">
                                {{ $reg->course->title }}
                            </h3>
                            <p class="mt-3 text-xs text-(--color-text-subtle)">
                                Terdaftar {{ $reg->created_at->diffForHumans() }}
                            </p>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ============== AKTIVITAS TERBARU ============== --}}
        <section class="mt-12">
            <h2 class="font-display text-2xl tracking-tight text-(--color-text) mb-4">Aktivitas terbaru</h2>

            @if ($activities->isEmpty())
                <div class="rounded-xl border border-dashed border-(--color-border-strong) bg-(--color-surface) p-6 text-center">
                    <p class="text-(--color-text-muted) text-sm">Belum ada aktivitas.</p>
                </div>
            @else
                <ul role="list" class="space-y-2">
                    @foreach ($activities as $a)
                        <li class="flex items-start gap-3 p-4 rounded-xl bg-(--color-surface) border border-(--color-border)">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg shrink-0
                                @switch($a['icon'])
                                    @case('book') bg-(--color-brand-50) text-(--color-brand-700) @break
                                    @case('check-badge') bg-(--color-success-soft) text-(--color-success) @break
                                    @case('star') bg-(--color-accent-100) text-(--color-accent-600) @break
                                    @default bg-(--color-surface-2) text-(--color-text-muted)
                                @endswitch">
                                @switch($a['icon'])
                                    @case('book')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    @break

                                    @case('check-badge')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                        </svg>
                                    @break

                                    @case('star')
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                            fill="currentColor" class="w-5 h-5" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.007Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @break
                                @endswitch
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-(--color-text)">{{ $a['title'] }}</p>
                                <p class="text-xs text-(--color-text-subtle) mt-0.5">
                                    {{ $a['timestamp']->diffForHumans() }}
                                </p>
                            </div>
                            @if ($a['url'])
                                <a href="{{ $a['url'] }}"
                                    class="text-sm font-medium text-(--color-brand-600) hover:text-(--color-brand-700) shrink-0">
                                    Buka
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

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
