@extends('layouts.app')

@section('title', 'KursusApp — Belajar skill baru dari mentor profesional')

@section('content')

    {{-- ============== HERO ============== --}}
    <section class="relative overflow-hidden">
        {{-- Background ornament --}}
        <div aria-hidden="true"
            class="absolute inset-0 -z-10 bg-gradient-to-b from-(--color-brand-50) via-(--color-bg) to-(--color-bg)">
        </div>
        <div aria-hidden="true"
            class="absolute -top-32 -right-32 w-[36rem] h-[36rem] rounded-full bg-(--color-brand-100) blur-3xl opacity-60 -z-10">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-3xl">
                <span
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-(--color-surface) border border-(--color-border) text-xs font-semibold uppercase tracking-wider text-(--color-brand-700)">
                    <span class="w-1.5 h-1.5 rounded-full bg-(--color-accent-500)"></span>
                    Belajar tanpa batas
                </span>
                <h1
                    class="mt-6 font-display text-4xl sm:text-5xl lg:text-6xl leading-[1.05] tracking-tight text-(--color-text)">
                    Tingkatkan skill Anda<br>
                    bersama <span class="text-(--color-brand-600)">mentor profesional</span>
                </h1>
                <p class="mt-6 text-lg text-(--color-text-muted) max-w-2xl">
                    Akses ratusan kursus pilihan, belajar dari ahli di bidangnya, dan dapatkan sertifikat resmi
                    untuk setiap kursus yang Anda selesaikan.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('courses.index') }}"
                        class="inline-flex items-center justify-center h-12 px-6 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold shadow-sm hover:bg-(--color-brand-700) transition">
                        Mulai jelajah kursus
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4 ml-2" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    @guest
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center h-12 px-6 rounded-lg bg-(--color-surface) text-(--color-text) border border-(--color-border-strong) text-sm font-semibold hover:bg-(--color-surface-2) transition">
                            Buat akun gratis
                        </a>
                    @endguest
                </div>

                {{-- Social proof strip --}}
                <dl class="mt-12 grid grid-cols-3 gap-6 max-w-lg">
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Kursus</dt>
                        <dd class="mt-1 font-display text-3xl text-(--color-text) tabular-nums">{{ $courses->count() ?? 0 }}+</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Mentor</dt>
                        <dd class="mt-1 font-display text-3xl text-(--color-text) tabular-nums">25+</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Siswa</dt>
                        <dd class="mt-1 font-display text-3xl text-(--color-text) tabular-nums">1.2k</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- ============== DAFTAR KURSUS ============== --}}
    <section id="kursus" class="py-20 md:py-24 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
                <div>
                    <span
                        class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">Katalog</span>
                    <h2 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">
                        Kursus pilihan
                    </h2>
                    <p class="mt-2 text-(--color-text-muted) max-w-xl">
                        Temukan kursus yang tepat untuk mendukung perjalanan karier Anda.
                    </p>
                </div>
                <a href="{{ route('courses.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-(--color-brand-600) hover:text-(--color-brand-700) transition">
                    Lihat semua kursus
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse($courses as $course)
                    <article
                        class="group bg-(--color-surface) rounded-xl border border-(--color-border) overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200">

                        {{-- Thumbnail --}}
                        @php
                            $thumb = collect($course->images ?? [])->filter()->first();
                            $imageCount = collect($course->images ?? [])->filter()->count();
                        @endphp
                        <div class="relative aspect-[16/10] bg-(--color-surface-2) overflow-hidden">
                            @if ($thumb)
                                <img src="{{ asset('storage/' . $thumb) }}"
                                    alt="Gambar kursus {{ $course->title }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    loading="lazy">
                                @if ($imageCount > 1)
                                    <span
                                        class="absolute bottom-3 right-3 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-black/60 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M1 5.25A2.25 2.25 0 0 1 3.25 3h13.5A2.25 2.25 0 0 1 19 5.25v9.5A2.25 2.25 0 0 1 16.75 17H3.25A2.25 2.25 0 0 1 1 14.75v-9.5Zm1.5 5.81v3.69c0 .414.336.75.75.75h13.5a.75.75 0 0 0 .75-.75v-2.69l-2.22-2.219a.75.75 0 0 0-1.06 0l-1.91 1.909.47.47a.75.75 0 1 1-1.06 1.06L6.53 8.091a.75.75 0 0 0-1.06 0L2.5 11.06ZM12 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $imageCount }}
                                    </span>
                                @endif
                            @else
                                <div aria-hidden="true"
                                    class="absolute inset-0 bg-gradient-to-br from-(--color-brand-100) to-(--color-accent-100)">
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="font-display text-5xl text-(--color-brand-700)/40">
                                        {{ strtoupper(mb_substr($course->title, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            @if ($loop->first)
                                <span
                                    class="absolute top-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-(--color-accent-500) text-(--color-text)">
                                    Bestseller
                                </span>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div class="p-5">
                            <span class="text-xs uppercase tracking-wider font-semibold text-(--color-text-subtle)">
                                Kursus
                            </span>
                            <h3 class="mt-1 text-lg font-semibold text-(--color-text) line-clamp-2 leading-snug">
                                <a href="{{ route('courses.show', $course->id) }}" class="hover:text-(--color-brand-600) transition">
                                    {{ $course->title }}
                                </a>
                            </h3>
                            <p class="mt-2 text-sm text-(--color-text-muted) line-clamp-2">
                                {{ Str::limit($course->description, 90) }}
                            </p>

                            <div class="mt-5 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-xs text-(--color-text-subtle)">Harga</p>
                                    <p class="text-lg font-semibold text-(--color-text) tabular-nums">
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('courses.show', $course->id) }}"
                                    class="inline-flex items-center justify-center h-10 px-4 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) transition">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-16 rounded-xl border border-dashed border-(--color-border-strong) bg-(--color-surface)">
                            <p class="text-(--color-text-muted)">Belum ada kursus yang tersedia.</p>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>
    </section>

    {{-- ============== TESTIMONI ============== --}}
    <section class="py-20 md:py-24 bg-(--color-surface-2) border-y border-(--color-border)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">Testimoni</span>
                <h2 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">
                    Dipercaya ribuan siswa
                </h2>
                <p class="mt-3 text-(--color-text-muted)">
                    Cerita nyata dari mereka yang telah berkembang bersama kami.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @forelse($testimonials as $t)
                    <figure
                        class="flex flex-col bg-(--color-surface) p-6 rounded-xl border border-(--color-border) shadow-sm">
                        <div class="flex items-center gap-1 text-(--color-accent-500)" aria-label="Rating 5 dari 5">
                            @for ($i = 0; $i < 5; $i++)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.007Z"
                                        clip-rule="evenodd" />
                                </svg>
                            @endfor
                        </div>
                        <blockquote class="mt-4 text-(--color-text) leading-relaxed flex-1">
                            "{{ $t->comment }}"
                        </blockquote>
                        <figcaption class="mt-5 flex items-center gap-3 pt-5 border-t border-(--color-border)">
                            <span
                                class="w-10 h-10 rounded-full bg-(--color-brand-100) text-(--color-brand-700) flex items-center justify-center font-semibold">
                                {{ strtoupper(mb_substr($t->user->name ?? 'U', 0, 1)) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-(--color-text)">{{ $t->user->name ?? 'Pengguna' }}</p>
                                <p class="text-xs text-(--color-text-subtle)">Siswa KursusApp</p>
                            </div>
                        </figcaption>
                    </figure>
                @empty
                    <div class="col-span-full text-center py-12 text-(--color-text-muted)">
                        Belum ada testimoni.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============== CTA FINAL ============== --}}
    @guest
        <section class="py-20 md:py-24">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-2xl bg-(--color-brand-700) text-(--color-text-on-brand) p-10 md:p-14 text-center">
                    <div aria-hidden="true"
                        class="absolute inset-0 bg-gradient-to-br from-(--color-brand-700) to-(--color-brand-900)">
                    </div>
                    <div class="relative">
                        <h2 class="font-display text-3xl md:text-4xl tracking-tight">
                            Siap memulai perjalanan belajar Anda?
                        </h2>
                        <p class="mt-4 text-white/80 max-w-xl mx-auto">
                            Bergabunglah dengan ribuan siswa yang telah meningkatkan karier mereka bersama KursusApp.
                        </p>
                        <a href="{{ route('register') }}"
                            class="mt-8 inline-flex items-center justify-center h-12 px-6 rounded-lg bg-(--color-accent-500) text-(--color-text) text-sm font-semibold shadow-sm hover:bg-(--color-accent-600) transition">
                            Daftar gratis sekarang
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endguest

@endsection
