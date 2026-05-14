@extends('layouts.app')

@section('title', 'Katalog Kursus — KursusApp')

@php
    // Build human-readable list of active filter chips
    $activeChips = [];
    if ($filters['q']) {
        $activeChips[] = [
            'label' => 'Cari: "'.$filters['q'].'"',
            'remove' => array_merge(request()->except(['q', 'page'])),
        ];
    }
    if ($filters['price']) {
        $priceLabels = [
            'free' => 'Gratis',
            'low' => 'Di bawah Rp 500.000',
            'mid' => 'Rp 500.000 – Rp 1.000.000',
            'high' => 'Di atas Rp 1.000.000',
        ];
        $activeChips[] = [
            'label' => 'Harga: '.($priceLabels[$filters['price']] ?? $filters['price']),
            'remove' => array_merge(request()->except(['price', 'page'])),
        ];
    }
    if ($filters['status']) {
        $statusLabels = [
            'upcoming' => 'Akan datang',
            'running' => 'Sedang berjalan',
            'past' => 'Sudah selesai',
        ];
        $activeChips[] = [
            'label' => 'Status: '.($statusLabels[$filters['status']] ?? $filters['status']),
            'remove' => array_merge(request()->except(['status', 'page'])),
        ];
    }
    foreach ($filters['mentor'] as $mentorId) {
        $mentor = $mentors->firstWhere('id', $mentorId);
        if (! $mentor) {
            continue;
        }
        $remainingMentors = array_values(array_diff($filters['mentor'], [$mentorId]));
        $activeChips[] = [
            'label' => 'Mentor: '.($mentor->user?->name ?? '—'),
            'remove' => array_merge(request()->except(['mentor', 'page']), ['mentor' => $remainingMentors]),
        ];
    }
@endphp

@section('content')
    {{-- ============== HERO ============== --}}
    <section class="relative overflow-hidden border-b border-(--color-border)">
        <div aria-hidden="true"
            class="absolute inset-0 -z-10 bg-gradient-to-b from-(--color-brand-50) via-(--color-bg) to-(--color-bg)"></div>
        <div aria-hidden="true"
            class="absolute -top-32 -right-32 w-[28rem] h-[28rem] rounded-full bg-(--color-brand-100) blur-3xl opacity-60 -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-end">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-(--color-surface) border border-(--color-border) text-xs font-semibold uppercase tracking-wider text-(--color-brand-700)">
                        <span class="w-1.5 h-1.5 rounded-full bg-(--color-accent-500)"></span>
                        Katalog Kursus
                    </span>
                    <h1 class="mt-4 font-display text-3xl sm:text-4xl md:text-5xl leading-tight tracking-tight text-(--color-text)">
                        Temukan kursus<br class="hidden sm:block">
                        yang <span class="text-(--color-brand-600)">tepat untuk Anda</span>
                    </h1>
                    <p class="mt-4 text-(--color-text-muted) max-w-2xl">
                        Filter dan urutkan untuk menemukan kursus yang sesuai dengan minat, jadwal, dan budget Anda.
                    </p>
                </div>

                {{-- Live stats --}}
                <dl class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 lg:max-w-xs gap-3">
                    <div class="bg-(--color-surface) rounded-xl border border-(--color-border) p-4">
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Kursus</dt>
                        <dd class="mt-1 font-display text-2xl text-(--color-text) tabular-nums">
                            {{ $stats['total_courses'] }}
                        </dd>
                    </div>
                    <div class="bg-(--color-surface) rounded-xl border border-(--color-border) p-4">
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Mentor</dt>
                        <dd class="mt-1 font-display text-2xl text-(--color-text) tabular-nums">
                            {{ $stats['total_mentors'] }}
                        </dd>
                    </div>
                    <div class="bg-(--color-surface) rounded-xl border border-(--color-border) p-4">
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Gratis</dt>
                        <dd class="mt-1 font-display text-2xl text-(--color-success) tabular-nums">
                            {{ $stats['free_courses'] }}
                        </dd>
                    </div>
                    <div class="bg-(--color-surface) rounded-xl border border-(--color-border) p-4">
                        <dt class="text-xs uppercase tracking-wider text-(--color-text-subtle)">Rata-rata</dt>
                        <dd class="mt-1 font-display text-xl text-(--color-text) tabular-nums truncate">
                            Rp {{ number_format($stats['avg_price'], 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- ============== BODY ============== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10"
        x-data="{
            view: localStorage.getItem('courses-view') || 'grid',
            mobileFilter: false,
            init() { this.$watch('view', v => localStorage.setItem('courses-view', v)) },
            autoSubmit() { document.getElementById('filterForm').requestSubmit() },
            debounceSubmit() {
                clearTimeout(this._t);
                this._t = setTimeout(() => this.autoSubmit(), 400);
            }
        }"
        x-cloak>

        <form method="GET" action="{{ route('courses.index') }}" id="filterForm" class="contents">

            {{-- ============== TOOLBAR ============== --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-5">
                {{-- Search --}}
                <div class="flex-1 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-(--color-text-subtle)" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                            clip-rule="evenodd" />
                    </svg>
                    <input type="search" name="q" value="{{ $filters['q'] }}"
                        @input="debounceSubmit()"
                        placeholder="Cari kursus, materi, atau topik…"
                        class="w-full h-11 pl-10 pr-4 rounded-lg border border-(--color-border-strong) bg-(--color-surface) text-sm text-(--color-text) placeholder:text-(--color-text-subtle) focus:outline-none focus:ring-2 focus:ring-(--color-brand-600) focus:border-transparent transition">
                </div>

                {{-- Sort --}}
                <select name="sort" @change="autoSubmit()"
                    class="h-11 px-3 pr-9 rounded-lg border border-(--color-border-strong) bg-(--color-surface) text-sm text-(--color-text) focus:outline-none focus:ring-2 focus:ring-(--color-brand-600)">
                    <option value="latest" @selected($filters['sort'] === 'latest')>Terbaru</option>
                    <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Harga terendah</option>
                    <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Harga tertinggi</option>
                    <option value="start_soon" @selected($filters['sort'] === 'start_soon')>Segera dimulai</option>
                </select>

                {{-- View toggle --}}
                <div class="hidden sm:inline-flex h-11 rounded-lg border border-(--color-border-strong) bg-(--color-surface) p-1" role="group" aria-label="Tampilan">
                    <button type="button" @click="view = 'grid'"
                        :class="view === 'grid' ? 'bg-(--color-brand-600) text-(--color-text-on-brand)' : 'text-(--color-text-muted) hover:text-(--color-text)'"
                        class="inline-flex items-center justify-center w-9 h-full rounded-md transition"
                        :aria-pressed="view === 'grid'" aria-label="Tampilan kisi">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4" aria-hidden="true">
                            <path d="M5.5 3A2.5 2.5 0 0 0 3 5.5v2A2.5 2.5 0 0 0 5.5 10h2A2.5 2.5 0 0 0 10 7.5v-2A2.5 2.5 0 0 0 7.5 3h-2ZM12.5 3A2.5 2.5 0 0 0 10 5.5v2A2.5 2.5 0 0 0 12.5 10h2A2.5 2.5 0 0 0 17 7.5v-2A2.5 2.5 0 0 0 14.5 3h-2ZM5.5 10A2.5 2.5 0 0 0 3 12.5v2A2.5 2.5 0 0 0 5.5 17h2A2.5 2.5 0 0 0 10 14.5v-2A2.5 2.5 0 0 0 7.5 10h-2ZM12.5 10a2.5 2.5 0 0 0-2.5 2.5v2a2.5 2.5 0 0 0 2.5 2.5h2a2.5 2.5 0 0 0 2.5-2.5v-2a2.5 2.5 0 0 0-2.5-2.5h-2Z" />
                        </svg>
                    </button>
                    <button type="button" @click="view = 'list'"
                        :class="view === 'list' ? 'bg-(--color-brand-600) text-(--color-text-on-brand)' : 'text-(--color-text-muted) hover:text-(--color-text)'"
                        class="inline-flex items-center justify-center w-9 h-full rounded-md transition"
                        :aria-pressed="view === 'list'" aria-label="Tampilan daftar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M3 4.75A.75.75 0 0 1 3.75 4h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 4.75ZM3 10a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm0 5.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                {{-- Mobile filter toggle --}}
                <button type="button" @click="mobileFilter = !mobileFilter"
                    class="lg:hidden inline-flex items-center justify-center h-11 px-4 rounded-lg border border-(--color-border-strong) bg-(--color-surface) text-(--color-text) text-sm font-semibold hover:bg-(--color-surface-2) transition">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4 mr-1.5" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 0 1 .628.74v2.288a2.25 2.25 0 0 1-.659 1.59l-4.682 4.683a2.25 2.25 0 0 0-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 0 1 8 18.25v-5.757a2.25 2.25 0 0 0-.659-1.591L2.659 6.22A2.25 2.25 0 0 1 2 4.629V2.34a.75.75 0 0 1 .628-.74Z"
                            clip-rule="evenodd" />
                    </svg>
                    Filter
                    @if (count($activeChips) > 0)
                        <span class="ml-2 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-(--color-brand-600) text-(--color-text-on-brand) text-xs font-bold">
                            {{ count($activeChips) }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- ============== ACTIVE FILTER CHIPS ============== --}}
            @if (count($activeChips) > 0)
                <div class="flex flex-wrap items-center gap-2 mb-5 pb-5 border-b border-(--color-border)">
                    <span class="text-xs uppercase tracking-wider font-semibold text-(--color-text-subtle) mr-1">Filter aktif:</span>
                    @foreach ($activeChips as $chip)
                        <a href="{{ route('courses.index', $chip['remove']) }}"
                            class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full bg-(--color-brand-50) border border-(--color-brand-200) text-(--color-brand-700) text-xs font-medium hover:bg-(--color-brand-100) transition">
                            {{ $chip['label'] }}
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-3.5 h-3.5" aria-hidden="true">
                                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </a>
                    @endforeach
                    <a href="{{ route('courses.index') }}"
                        class="ml-1 text-xs font-semibold text-(--color-error) hover:underline">
                        Hapus semua
                    </a>
                </div>
            @endif

            {{-- ============== LAYOUT ============== --}}
            <div class="grid lg:grid-cols-[18rem_1fr] gap-6">

                {{-- ===== SIDEBAR FILTER ===== --}}
                <aside :class="mobileFilter ? 'block' : 'hidden lg:block'"
                    class="lg:sticky lg:top-24 self-start space-y-4">

                    {{-- Rentang harga --}}
                    <div class="bg-(--color-surface) rounded-xl border border-(--color-border) overflow-hidden"
                        x-data="{ open: true }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 hover:bg-(--color-surface-2) transition">
                            <h3 class="text-sm font-semibold text-(--color-text)">Rentang harga</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 text-(--color-text-muted) transition-transform" :class="open && 'rotate-180'"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.150ms class="px-5 pb-4 space-y-2">
                            @foreach ([
                                'all' => 'Semua harga',
                                'free' => 'Gratis',
                                'low' => 'Di bawah Rp 500.000',
                                'mid' => 'Rp 500.000 – Rp 1.000.000',
                                'high' => 'Di atas Rp 1.000.000',
                            ] as $key => $label)
                                @php $val = $key === 'all' ? null : $key; @endphp
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="radio" name="price" value="{{ $val }}"
                                        @checked(($filters['price'] ?? null) === $val)
                                        @change="autoSubmit()"
                                        class="w-4 h-4 text-(--color-brand-600) border-(--color-border-strong) focus:ring-(--color-brand-600)">
                                    <span class="text-sm text-(--color-text) group-hover:text-(--color-brand-600) transition">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Mentor --}}
                    @if ($mentors->isNotEmpty())
                        <div class="bg-(--color-surface) rounded-xl border border-(--color-border) overflow-hidden"
                            x-data="{ open: true, q: '' }">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-5 py-4 hover:bg-(--color-surface-2) transition">
                                <h3 class="text-sm font-semibold text-(--color-text)">
                                    Mentor
                                    @if (count($filters['mentor']) > 0)
                                        <span class="ml-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full bg-(--color-brand-600) text-(--color-text-on-brand) text-xs font-bold">
                                            {{ count($filters['mentor']) }}
                                        </span>
                                    @endif
                                </h3>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-(--color-text-muted) transition-transform" :class="open && 'rotate-180'"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.150ms class="px-5 pb-4">
                                @if ($mentors->count() > 5)
                                    <input type="text" x-model="q" placeholder="Cari mentor…"
                                        class="w-full h-9 px-3 mb-3 rounded-lg border border-(--color-border) bg-(--color-surface-2) text-sm text-(--color-text) placeholder:text-(--color-text-subtle) focus:outline-none focus:ring-2 focus:ring-(--color-brand-600)">
                                @endif
                                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                    @foreach ($mentors as $mentor)
                                        <label class="flex items-center gap-2.5 cursor-pointer group"
                                            x-show="q === '' || '{{ strtolower(addslashes($mentor->user?->name ?? '')) }}'.includes(q.toLowerCase())">
                                            <input type="checkbox" name="mentor[]" value="{{ $mentor->id }}"
                                                @checked(in_array($mentor->id, $filters['mentor'], true))
                                                @change="autoSubmit()"
                                                class="w-4 h-4 rounded text-(--color-brand-600) border-(--color-border-strong) focus:ring-(--color-brand-600)">
                                            @if ($mentor->photo)
                                                <img src="{{ asset('storage/' . $mentor->photo) }}"
                                                    alt="" class="w-5 h-5 rounded-full object-cover" loading="lazy">
                                            @else
                                                <span class="w-5 h-5 rounded-full bg-(--color-brand-100) text-(--color-brand-700) flex items-center justify-center text-[10px] font-bold">
                                                    {{ strtoupper(mb_substr($mentor->user?->name ?? '?', 0, 1)) }}
                                                </span>
                                            @endif
                                            <span class="text-sm text-(--color-text) group-hover:text-(--color-brand-600) transition truncate">
                                                {{ $mentor->user?->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Status jadwal --}}
                    <div class="bg-(--color-surface) rounded-xl border border-(--color-border) overflow-hidden"
                        x-data="{ open: true }">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-5 py-4 hover:bg-(--color-surface-2) transition">
                            <h3 class="text-sm font-semibold text-(--color-text)">Status jadwal</h3>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 text-(--color-text-muted) transition-transform" :class="open && 'rotate-180'"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.150ms class="px-5 pb-4 space-y-2">
                            @foreach ([
                                'all' => 'Semua status',
                                'upcoming' => 'Akan datang',
                                'running' => 'Sedang berjalan',
                                'past' => 'Sudah selesai',
                            ] as $key => $label)
                                @php $val = $key === 'all' ? null : $key; @endphp
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="radio" name="status" value="{{ $val }}"
                                        @checked(($filters['status'] ?? null) === $val)
                                        @change="autoSubmit()"
                                        class="w-4 h-4 text-(--color-brand-600) border-(--color-border-strong) focus:ring-(--color-brand-600)">
                                    <span class="text-sm text-(--color-text) group-hover:text-(--color-brand-600) transition">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('courses.index') }}"
                        class="block w-full text-center h-11 inline-flex items-center justify-center rounded-lg border border-(--color-border-strong) bg-(--color-surface) text-(--color-text) text-sm font-semibold hover:bg-(--color-surface-2) transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4 mr-1.5" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                clip-rule="evenodd" />
                        </svg>
                        Reset filter
                    </a>
                </aside>

                {{-- ===== KONTEN UTAMA ===== --}}
                <div>
                    {{-- Result meta --}}
                    <div class="flex items-center justify-between mb-5">
                        <p class="text-sm text-(--color-text-muted)">
                            Menampilkan <span class="font-semibold text-(--color-text) tabular-nums">{{ $courses->total() }}</span>
                            kursus
                        </p>
                    </div>

                    @if ($courses->isEmpty())
                        <div class="text-center py-20 rounded-xl border border-dashed border-(--color-border-strong) bg-(--color-surface)">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-(--color-surface-2) text-(--color-text-subtle)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-8 h-8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                            <p class="mt-4 font-display text-xl text-(--color-text)">Tidak ada kursus yang cocok</p>
                            <p class="mt-1 text-sm text-(--color-text-muted) max-w-sm mx-auto">
                                Coba ubah kata kunci pencarian, hapus beberapa filter, atau reset semuanya.
                            </p>
                            <a href="{{ route('courses.index') }}"
                                class="mt-6 inline-flex items-center justify-center h-11 px-5 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition">
                                Reset semua filter
                            </a>
                        </div>
                    @else
                        {{-- ===== GRID VIEW ===== --}}
                        <div x-show="view === 'grid'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                            @foreach ($courses as $course)
                                @include('courses.partials.card-grid', ['course' => $course])
                            @endforeach
                        </div>

                        {{-- ===== LIST VIEW ===== --}}
                        <div x-show="view === 'list'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            class="space-y-4" style="display: none;">
                            @foreach ($courses as $course)
                                @include('courses.partials.card-list', ['course' => $course])
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
