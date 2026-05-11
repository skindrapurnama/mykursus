<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Log::info($request->all());
        $orderId = $request->order_id;
        $status = $request->transaction_status;

        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) return response()->json(['message' => 'Not found']);

        if ($status == 'settlement' || $status == 'capture') {
            $payment->update(['status' => 'approved']);
        } elseif ($status == 'pending') {
            $payment->update(['status' => 'pending']);
        } else {
            $payment->update(['status' => 'rejected']);
        }

        return response()->json(['message' => 'OK']);
    }
    
}
