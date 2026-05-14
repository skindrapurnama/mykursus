<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Mentor;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->with(['mentors.user']);

        // Pencarian
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Rentang harga
        $priceRange = $request->input('price');
        match ($priceRange) {
            'free' => $query->where('price', 0),
            'low' => $query->whereBetween('price', [1, 500_000]),
            'mid' => $query->whereBetween('price', [500_001, 1_000_000]),
            'high' => $query->where('price', '>', 1_000_000),
            default => null,
        };

        if (is_numeric($request->input('min_price'))) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }
        if (is_numeric($request->input('max_price'))) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        // Filter mentor (multi)
        $selectedMentors = collect($request->input('mentor', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($selectedMentors->isNotEmpty()) {
            $query->whereHas('mentors', function ($q) use ($selectedMentors): void {
                $q->whereIn('mentors.id', $selectedMentors->all());
            });
        }

        // Status jadwal
        $today = now()->toDateString();
        match ($request->input('status')) {
            'upcoming' => $query->whereDate('start_date', '>', $today),
            'running' => $query->where(function ($q) use ($today): void {
                $q->where(function ($q2) use ($today): void {
                    $q2->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                })->orWhere(function ($q2): void {
                    $q2->whereNull('start_date')->whereNull('end_date');
                });
            }),
            'past' => $query->whereDate('end_date', '<', $today),
            default => null,
        };

        // Urutan
        match ($request->input('sort', 'latest')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'start_soon' => $query->orderByRaw('start_date IS NULL, start_date ASC'),
            default => $query->latest(),
        };

        $courses = $query->paginate(12)->withQueryString();

        $mentors = Mentor::query()
            ->with('user')
            ->where('is_active', true)
            ->whereHas('courses')
            ->get()
            ->sortBy(fn (Mentor $m) => $m->user?->name ?? '')
            ->values();

        $stats = [
            'total_courses' => Course::count(),
            'total_mentors' => Mentor::where('is_active', true)->count(),
            'free_courses' => Course::where('price', 0)->count(),
            'avg_price' => (int) Course::where('price', '>', 0)->avg('price'),
        ];

        return view('courses.index', [
            'courses' => $courses,
            'mentors' => $mentors,
            'stats' => $stats,
            'filters' => [
                'q' => $search,
                'price' => $priceRange,
                'min_price' => $request->input('min_price'),
                'max_price' => $request->input('max_price'),
                'mentor' => $selectedMentors->all(),
                'status' => $request->input('status'),
                'sort' => $request->input('sort', 'latest'),
            ],
        ]);
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function register(Course $course)
    {
        $user = auth()->user();

        $registration = Registration::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        // buat payment
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'amount' => $course->price,
            'status' => 'pending',
        ]);

        // setup midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // data transaksi
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $payment->id . '-' . time(),
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];
        $snapToken = Snap::getSnapToken($params);

        // simpan token
        $payment->update([
            'snap_token' => $snapToken,
            'order_id' => $params['transaction_details']['order_id'],
        ]);

        $snapToken = \Midtrans\Snap::getSnapToken($params);
        // dd($snapToken);
        return view('payment.snap', compact('snapToken'));
        // return redirect()->route('courses.show', $course)
        //     ->with('success', 'Berhasil daftar, silakan upload pembayaran.');
    }

    public function retryPayment($id)
    {
        $payment = \App\Models\Payment::findOrFail($id);

        // reset status
        $payment->update([
            'status' => 'pending'
        ]);

        // redirect ke halaman bayar lagi
        return redirect()->route('courses.show', $payment->registration->course_id)
            ->with('success', 'Silakan lakukan pembayaran ulang');
    }

    public function uploadPayment(Request $request, Registration $registration)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048'
        ]);

        $file = $request->file('payment_proof')->store('payments', 'public');

        Payment::create([
            'registration_id' => $registration->id,
            'amount' => $registration->course->price,
            'payment_method' => 'transfer',
            'payment_proof' => $file,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload');
    }
}
