@extends('layouts.app')

@section('title', 'Pembayaran — KursusApp')

@section('content')
    <div class="max-w-xl mx-auto px-4 sm:px-6 py-12 md:py-16">

        <div class="text-center mb-8">
            <span class="text-xs font-semibold uppercase tracking-wider text-(--color-brand-600)">Pembayaran</span>
            <h1 class="mt-2 font-display text-3xl md:text-4xl tracking-tight text-(--color-text)">
                Selesaikan pembayaran
            </h1>
            <p class="mt-2 text-(--color-text-muted)">
                Pilih metode pembayaran favorit Anda untuk melanjutkan.
            </p>
        </div>

        <div class="bg-(--color-surface) rounded-2xl border border-(--color-border) shadow-md p-6 sm:p-8">

            {{-- Trust badges --}}
            <ul class="grid grid-cols-2 gap-3 mb-6 text-xs" role="list">
                <li class="flex items-center gap-2 p-3 rounded-lg bg-(--color-surface-2) text-(--color-text)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4 text-(--color-success)" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z"
                            clip-rule="evenodd" />
                    </svg>
                    Transaksi aman
                </li>
                <li class="flex items-center gap-2 p-3 rounded-lg bg-(--color-surface-2) text-(--color-text)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4 text-(--color-success)" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                            clip-rule="evenodd" />
                    </svg>
                    Proses instan
                </li>
            </ul>

            <button id="pay-button" type="button"
                class="w-full h-12 inline-flex items-center justify-center rounded-lg bg-(--color-brand-600) text-(--color-text-on-brand) text-sm font-semibold hover:bg-(--color-brand-700) shadow-sm transition disabled:opacity-60 disabled:cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>
                Bayar Sekarang
            </button>

            <p class="mt-4 text-center text-xs text-(--color-text-subtle)">
                Diproses oleh <span class="font-semibold text-(--color-text)">Midtrans</span> · Sertifikat PCI DSS
            </p>
        </div>

        <p class="mt-6 text-center text-sm">
            <a href="{{ url('/my-courses') }}"
                class="text-(--color-text-muted) hover:text-(--color-brand-600) inline-flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"
                    aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z"
                        clip-rule="evenodd" />
                </svg>
                Kembali ke Kursus Saya
            </a>
        </p>
    </div>

    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script>
            (function() {
                const btn = document.getElementById('pay-button');
                if (!btn) return;
                btn.addEventListener('click', function() {
                    btn.disabled = true;
                    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>Memproses…';
                    snap.pay('{{ $snapToken }}', {
                        onSuccess: function() {
                            window.location.href = "/my-courses";
                        },
                        onPending: function() {
                            window.location.href = "/my-courses";
                        },
                        onError: function() {
                            btn.disabled = false;
                            btn.innerText = 'Bayar Sekarang';
                            alert("Pembayaran gagal. Silakan coba lagi.");
                        },
                        onClose: function() {
                            btn.disabled = false;
                            btn.innerText = 'Bayar Sekarang';
                        }
                    });
                });
            })();
        </script>
    @endpush
@endsection
