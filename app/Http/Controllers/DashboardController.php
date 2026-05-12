<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $registrations = Registration::with('course')
            ->where('user_id', $userId)
            ->get();

        $continueLearning = $registrations
            ->where('status', 'approved')
            ->sortByDesc('created_at')
            ->take(3)
            ->values();

        $payments = Payment::with('registration.course')
            ->whereHas('registration', fn ($q) => $q->where('user_id', $userId))
            ->whereIn('status', ['approved', 'paid'])
            ->get();

        $certificates = Certificate::with('course')
            ->where('user_id', $userId)
            ->get();

        $activities = $registrations->map(fn ($r) => [
            'type' => 'registration',
            'title' => 'Mendaftar kursus '.$r->course->title,
            'timestamp' => $r->created_at,
            'icon' => 'book',
            'url' => route('courses.show', $r->course_id),
        ])
            ->concat($payments->map(fn ($p) => [
                'type' => 'payment',
                'title' => 'Pembayaran berhasil untuk '.$p->registration->course->title,
                'timestamp' => $p->paid_at ?? $p->updated_at,
                'icon' => 'check-badge',
                'url' => null,
            ]))
            ->concat($certificates->map(fn ($c) => [
                'type' => 'certificate',
                'title' => 'Sertifikat diterbitkan: '.$c->course->title,
                'timestamp' => $c->issued_at ?? $c->created_at,
                'icon' => 'star',
                'url' => route('certificate.download', $c->id),
            ]))
            ->sortByDesc('timestamp')
            ->take(5)
            ->values();

        return view('dashboard', [
            'coursesCount' => $registrations->count(),
            'certificatesCount' => $certificates->count(),
            'continueLearning' => $continueLearning,
            'activities' => $activities,
        ]);
    }
}
