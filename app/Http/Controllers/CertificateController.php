<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View
    {
        $certificates = Certificate::with('course')
            ->where('user_id', auth()->id())
            ->latest('issued_at')
            ->get();

        return view('certificates', compact('certificates'));
    }
}
