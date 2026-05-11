@extends('layouts.app')

@section('title', 'Sertifikat Saya — KursusApp')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">

        <header class="mb-8">
            <span class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">Pencapaian</span>
            <h1 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">Sertifikat Saya</h1>
            <p class="mt-2 text-(--color-text-muted)">Semua sertifikat yang telah Anda peroleh.</p>
        </header>

        @if ($certificates->isEmpty())
            <div class="rounded-xl border border-dashed border-(--color-border-strong) bg-(--color-surface) p-10 text-center">
                <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-(--color-accent-100) text-(--color-accent-600) mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-7 h-7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 18.75h-9a9.7 9.7 0 0 1-.412-.011c-1.1-.05-1.838-.834-1.838-1.989V7.5c0-1.155.738-1.939 1.838-1.989a9.7 9.7 0 0 1 .412-.011h9a9.7 9.7 0 0 1 .412.011c1.1.05 1.838.834 1.838 1.989v9.25c0 1.155-.738 1.939-1.838 1.989a9.7 9.7 0 0 1-.412.011Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 10.5 14.25 15 9.75" />
                    </svg>
                </span>
                <h2 class="font-semibold text-(--color-text)">Belum ada sertifikat</h2>
                <p class="mt-1 text-sm text-(--color-text-muted)">Selesaikan kursus untuk mendapatkan sertifikat.</p>
                <a href="/#kursus"
                    class="mt-5 inline-flex items-center justify-center h-10 px-5 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) transition">
                    Jelajahi kursus
                </a>
            </div>
        @else
            <ul class="space-y-3" role="list">
                @foreach ($certificates as $cert)
                    <li>
                        <article
                            class="flex flex-col sm:flex-row sm:items-center gap-4 p-5 rounded-xl bg-(--color-surface) border border-(--color-border) shadow-sm hover:shadow-md transition">

                            <span
                                class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-(--color-accent-100) text-(--color-accent-600) shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                </svg>
                            </span>

                            <div class="flex-1 min-w-0">
                                <h2 class="font-semibold text-(--color-text) truncate">{{ $cert->course->title }}</h2>
                                <p class="mt-0.5 text-sm text-(--color-text-muted)">
                                    Diterbitkan
                                    <time datetime="{{ $cert->issued_at }}" class="tabular-nums">
                                        {{ \Carbon\Carbon::parse($cert->issued_at)->translatedFormat('d M Y') }}
                                    </time>
                                </p>
                            </div>

                            <a href="{{ route('certificate.download', $cert->id) }}"
                                class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) transition self-start sm:self-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh
                            </a>
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>
@endsection
