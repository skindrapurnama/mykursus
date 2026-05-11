<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class CourseController extends Controller
{
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
