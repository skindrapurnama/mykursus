<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Certificate;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // hanya jalan kalau status berubah jadi paid/terbayar
        if ($payment->isDirty('status') && $payment->status === 'paid' || $payment->status === 'approved') {

            $registration = $payment->registration;

            if (!$registration) return;

            // ✅ update status registration
            $registration->update([
                'status' => 'approved'
            ]);

            // ✅ cek agar tidak duplicate sertifikat
            $exists = Certificate::where('user_id', $registration->user_id)
                ->where('course_id', $registration->course_id)
                ->exists();

            if (!$exists) {
                Certificate::create([
                    'user_id' => $registration->user_id,
                    'course_id' => $registration->course_id,
                    'certificate_number' => 'CERT-' . now()->format('Ymd') . '-' . rand(1000, 9999),
                    'issued_at' => now(),
                ]);
            }
        }

        if ($payment->isDirty('status') && $payment->status === 'rejected') {
            $registration = $payment->registration;

            if (!$registration) return;

            // ✅ update status registration
            $registration->update([
                'status' => 'rejected'
            ]);

            $exists = Certificate::where('user_id', $registration->user_id)
                ->where('course_id', $registration->course_id)
                ->delete();
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
