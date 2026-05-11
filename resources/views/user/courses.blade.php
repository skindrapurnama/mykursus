@extends('layouts.app')

@section('title', 'Kursus Saya — KursusApp')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">

        <header class="mb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">Belajar Anda</span>
                <h1 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">Kursus Saya</h1>
                <p class="mt-2 text-(--color-text-muted)">Pantau status kursus dan kelola pembayaran Anda.</p>
            </div>
            <a href="/#kursus"
                class="inline-flex items-center justify-center h-11 px-5 rounded-lg bg-(--color-surface) text-(--color-text) border border-(--color-border-strong) text-sm font-semibold hover:bg-(--color-surface-2) transition self-start">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-2"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"
                        clip-rule="evenodd" />
                </svg>
                Cari kursus baru
            </a>
        </header>

        @forelse($registrations as $reg)
            @php
                $regStatus = $reg->status;
                $payStatus = optional($reg->payment)->status;

                $regBadge = match ($regStatus) {
                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-(--color-success-soft) text-(--color-success)'],
                    'pending' => ['label' => 'Menunggu', 'class' => 'bg-(--color-warning-soft) text-(--color-warning)'],
                    default => ['label' => 'Ditolak', 'class' => 'bg-(--color-error-soft) text-(--color-error)'],
                };

                $payBadge = match ($payStatus) {
                    'approved' => ['label' => 'Lunas', 'class' => 'bg-(--color-success-soft) text-(--color-success)'],
                    'pending' => ['label' => 'Menunggu', 'class' => 'bg-(--color-warning-soft) text-(--color-warning)'],
                    'failed' => ['label' => 'Gagal', 'class' => 'bg-(--color-error-soft) text-(--color-error)'],
                    default => ['label' => 'Belum Bayar', 'class' => 'bg-(--color-surface-3) text-(--color-text-muted)'],
                };

                $certificate = \App\Models\Certificate::where('user_id', auth()->id())
                    ->where('course_id', $reg->course_id)
                    ->first();
            @endphp

            <article
                class="mb-4 bg-(--color-surface) rounded-2xl border border-(--color-border) shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="p-5 sm:p-6">

                    {{-- Header row --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                        <div class="flex items-start gap-4 min-w-0">
                            <span
                                class="hidden sm:inline-flex w-12 h-12 rounded-lg bg-(--color-brand-100) text-(--color-brand-700) items-center justify-center font-display text-xl shrink-0">
                                {{ strtoupper(mb_substr($reg->course->title, 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <h2 class="text-lg font-semibold text-(--color-text) leading-snug">
                                    {{ $reg->course->title }}
                                </h2>
                                <p class="mt-1 text-sm text-(--color-text-muted) line-clamp-2">
                                    {{ $reg->course->description }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $regBadge['class'] }} shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $regBadge['label'] }}
                        </span>
                    </div>

                    {{-- Meta row --}}
                    <dl class="mt-5 grid sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center justify-between sm:justify-start gap-3 p-3 rounded-lg bg-(--color-surface-2)">
                            <dt class="text-(--color-text-muted)">Status pembayaran</dt>
                            <dd>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $payBadge['class'] }}">
                                    {{ $payBadge['label'] }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between sm:justify-start gap-3 p-3 rounded-lg bg-(--color-surface-2)">
                            <dt class="text-(--color-text-muted)">Harga</dt>
                            <dd class="font-semibold text-(--color-text) tabular-nums">
                                Rp {{ number_format($reg->course->price, 0, ',', '.') }}
                            </dd>
                        </div>
                    </dl>

                    {{-- Actions --}}
                    <div class="mt-5 flex flex-wrap items-center gap-2">

                        @if ($payStatus !== 'approved')
                            <a href="{{ route('courses.show', $reg->course->id) }}"
                                class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                </svg>
                                Bayar Sekarang
                            </a>
                        @endif

                        @if ($certificate && $regStatus === 'approved')
                            <a href="{{ route('certificate.download', $certificate->id) }}"
                                class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-success) text-white text-sm font-semibold hover:opacity-90 shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh Sertifikat
                            </a>
                        @endif

                        @if ($reg->payment && $payStatus === 'failed')
                            <form method="POST" action="{{ route('payment.retry', $reg->payment->id) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-surface) text-(--color-warning) border border-(--color-warning)/30 text-sm font-semibold hover:bg-(--color-warning-soft) transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Coba Bayar Lagi
                                </button>
                            </form>
                        @endif

                        @if (config('app.debug') && $reg->payment)
                            <form method="POST" action="/simulate-payment/{{ $reg->payment->id }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-surface) text-(--color-text-muted) border border-dashed border-(--color-border-strong) text-sm font-medium hover:bg-(--color-surface-2) transition">
                                    🧪 Simulasi Sukses
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-(--color-border-strong) bg-(--color-surface) p-10 text-center">
                <span
                    class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-(--color-brand-50) text-(--color-brand-700) mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-7 h-7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </span>
                <h2 class="font-semibold text-(--color-text)">Belum ada kursus</h2>
                <p class="mt-1 text-sm text-(--color-text-muted)">Anda belum mendaftar ke kursus apapun.</p>
                <a href="/#kursus"
                    class="mt-5 inline-flex items-center justify-center h-10 px-5 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition">
                    Jelajahi kursus
                </a>
            </div>
        @endforelse
    </div>
@endsection
