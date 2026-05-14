@php
    $thumb = collect($course->images ?? [])->filter()->first();
    $imageCount = collect($course->images ?? [])->filter()->count();
    $primary = $course->mentors->firstWhere('pivot.role', 'primary') ?? $course->mentors->first();
    $otherMentors = $course->mentors->reject(fn ($m) => $primary && $m->id === $primary->id)->take(2);
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

<article class="group bg-(--color-surface) rounded-xl border border-(--color-border) overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-(--color-brand-200) transition-all duration-300 flex flex-col">

    <a href="{{ route('courses.show', $course->id) }}"
        class="relative aspect-[16/10] bg-(--color-surface-2) overflow-hidden block">
        @if ($thumb)
            <img src="{{ asset('storage/' . $thumb) }}"
                alt="Gambar kursus {{ $course->title }}"
                class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-500"
                loading="lazy">
            <div aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
        @else
            <div aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-br from-(--color-brand-100) to-(--color-accent-100) group-hover:scale-110 transition duration-500"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="font-display text-5xl text-(--color-brand-700)/40">
                    {{ strtoupper(mb_substr($course->title, 0, 1)) }}
                </span>
            </div>
        @endif

        {{-- Badges --}}
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

        @if ($imageCount > 1)
            <span class="absolute bottom-3 right-3 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-black/60 text-white backdrop-blur">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M1 5.25A2.25 2.25 0 0 1 3.25 3h13.5A2.25 2.25 0 0 1 19 5.25v9.5A2.25 2.25 0 0 1 16.75 17H3.25A2.25 2.25 0 0 1 1 14.75v-9.5Zm1.5 5.81v3.69c0 .414.336.75.75.75h13.5a.75.75 0 0 0 .75-.75v-2.69l-2.22-2.219a.75.75 0 0 0-1.06 0l-1.91 1.909.47.47a.75.75 0 1 1-1.06 1.06L6.53 8.091a.75.75 0 0 0-1.06 0L2.5 11.06ZM12 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"
                        clip-rule="evenodd" />
                </svg>
                {{ $imageCount }}
            </span>
        @endif
    </a>

    <div class="p-5 flex flex-col flex-1">
        <h3 class="text-base font-semibold text-(--color-text) line-clamp-2 leading-snug">
            <a href="{{ route('courses.show', $course->id) }}" class="hover:text-(--color-brand-600) transition">
                {{ $course->title }}
            </a>
        </h3>

        <p class="mt-2 text-sm text-(--color-text-muted) line-clamp-2">
            {{ Str::limit(strip_tags($course->description), 100) }}
        </p>

        {{-- Mentor stack --}}
        @if ($course->mentors->isNotEmpty())
            <div class="mt-3 flex items-center gap-2">
                <div class="flex -space-x-2">
                    @if ($primary)
                        @if ($primary->photo)
                            <img src="{{ asset('storage/' . $primary->photo) }}" alt="{{ $primary->user?->name }}"
                                class="w-7 h-7 rounded-full object-cover border-2 border-(--color-surface)" loading="lazy">
                        @else
                            <span class="w-7 h-7 rounded-full bg-(--color-brand-100) text-(--color-brand-700) flex items-center justify-center text-[10px] font-bold border-2 border-(--color-surface)">
                                {{ strtoupper(mb_substr($primary->user?->name ?? '?', 0, 1)) }}
                            </span>
                        @endif
                    @endif
                    @foreach ($otherMentors as $om)
                        @if ($om->photo)
                            <img src="{{ asset('storage/' . $om->photo) }}" alt="{{ $om->user?->name }}"
                                class="w-7 h-7 rounded-full object-cover border-2 border-(--color-surface)" loading="lazy">
                        @else
                            <span class="w-7 h-7 rounded-full bg-(--color-accent-100) text-(--color-accent-700) flex items-center justify-center text-[10px] font-bold border-2 border-(--color-surface)">
                                {{ strtoupper(mb_substr($om->user?->name ?? '?', 0, 1)) }}
                            </span>
                        @endif
                    @endforeach
                    @if ($course->mentors->count() > 3)
                        <span class="w-7 h-7 rounded-full bg-(--color-surface-2) text-(--color-text-muted) flex items-center justify-center text-[10px] font-bold border-2 border-(--color-surface)">
                            +{{ $course->mentors->count() - 3 }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-(--color-text-muted) truncate">
                    {{ $primary?->user?->name ?? '—' }}
                </span>
            </div>
        @endif

        {{-- Meta --}}
        <div class="mt-3 flex flex-wrap gap-2 text-xs">
            @if ($course->start_date)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-(--color-surface-2) text-(--color-text-muted)">
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
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-(--color-surface-2) text-(--color-text-muted)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    {{ $course->quota }} slot
                </span>
            @endif
        </div>

        <div class="mt-auto pt-4 flex items-end justify-between gap-3">
            <div>
                <p class="text-xs text-(--color-text-subtle)">Harga</p>
                <p class="text-lg font-semibold text-(--color-text) tabular-nums">
                    @if ((int) $course->price === 0)
                        Gratis
                    @else
                        Rp {{ number_format($course->price, 0, ',', '.') }}
                    @endif
                </p>
            </div>
            <a href="{{ route('courses.show', $course->id) }}"
                class="inline-flex items-center gap-1.5 justify-center h-10 px-4 rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition group/btn">
                Detail
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>
</article>
