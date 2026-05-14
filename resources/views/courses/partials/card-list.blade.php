@php
    $thumb = collect($course->images ?? [])->filter()->first();
    $primary = $course->mentors->firstWhere('pivot.role', 'primary') ?? $course->mentors->first();
    $today = now()->startOfDay();
    $status = match (true) {
        $course->start_date && $course->start_date->gt($today) => 'upcoming',
        $course->end_date && $course->end_date->lt($today) => 'past',
        $course->start_date || $course->end_date => 'running',
        default => null,
    };
    $statusBadge = [
        'upcoming' => ['label' => 'Akan datang', 'class' => 'bg-(--color-accent-100) text-(--color-accent-700)'],
        'running' => ['label' => 'Berjalan', 'class' => 'bg-(--color-brand-100) text-(--color-brand-700)'],
        'past' => ['label' => 'Selesai', 'class' => 'bg-(--color-surface-3) text-(--color-text-muted)'],
    ];
@endphp

<article class="group bg-(--color-surface) rounded-xl border border-(--color-border) overflow-hidden shadow-sm hover:shadow-md hover:border-(--color-brand-200) transition duration-200">
    <div class="grid sm:grid-cols-[14rem_1fr] gap-0">
        <a href="{{ route('courses.show', $course->id) }}"
            class="relative aspect-[16/10] sm:aspect-auto sm:h-full bg-(--color-surface-2) overflow-hidden block">
            @if ($thumb)
                <img src="{{ asset('storage/' . $thumb) }}"
                    alt="Gambar kursus {{ $course->title }}"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300"
                    loading="lazy">
            @else
                <div aria-hidden="true"
                    class="absolute inset-0 bg-gradient-to-br from-(--color-brand-100) to-(--color-accent-100)"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="font-display text-4xl text-(--color-brand-700)/40">
                        {{ strtoupper(mb_substr($course->title, 0, 1)) }}
                    </span>
                </div>
            @endif

            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                @if ((int) $course->price === 0)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-(--color-success) text-white shadow-sm">
                        Gratis
                    </span>
                @endif
                @if ($status && isset($statusBadge[$status]))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBadge[$status]['class'] }} shadow-sm">
                        {{ $statusBadge[$status]['label'] }}
                    </span>
                @endif
            </div>
        </a>

        <div class="p-5 flex flex-col gap-3">
            <div>
                <h3 class="text-lg font-semibold text-(--color-text) leading-snug">
                    <a href="{{ route('courses.show', $course->id) }}" class="hover:text-(--color-brand-600) transition">
                        {{ $course->title }}
                    </a>
                </h3>
                <p class="mt-1.5 text-sm text-(--color-text-muted) line-clamp-2">
                    {{ Str::limit(strip_tags($course->description), 200) }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-(--color-text-muted)">
                @if ($primary)
                    <span class="inline-flex items-center gap-1.5">
                        @if ($primary->photo)
                            <img src="{{ asset('storage/' . $primary->photo) }}" alt=""
                                class="w-5 h-5 rounded-full object-cover" loading="lazy">
                        @else
                            <span class="w-5 h-5 rounded-full bg-(--color-brand-100) text-(--color-brand-700) flex items-center justify-center text-[10px] font-bold">
                                {{ strtoupper(mb_substr($primary->user?->name ?? '?', 0, 1)) }}
                            </span>
                        @endif
                        <span class="text-(--color-text) font-medium">{{ $primary->user?->name }}</span>
                    </span>
                @endif
                @if ($course->start_date)
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-3.5 h-3.5" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $course->start_date->translatedFormat('d M Y') }}
                    </span>
                @endif
                @if ($course->quota)
                    <span class="inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Kuota {{ $course->quota }}
                    </span>
                @endif
            </div>

            <div class="mt-auto flex items-end justify-between gap-3 pt-2">
                <div>
                    <p class="text-xs text-(--color-text-subtle)">Harga</p>
                    <p class="text-xl font-semibold text-(--color-text) tabular-nums">
                        @if ((int) $course->price === 0)
                            Gratis
                        @else
                            Rp {{ number_format($course->price, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('courses.show', $course->id) }}"
                    class="inline-flex items-center gap-1.5 justify-center h-10 px-5 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition group/btn">
                    Lihat detail
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</article>
