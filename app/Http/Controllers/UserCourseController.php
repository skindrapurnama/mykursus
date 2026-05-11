<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Certificate;

class UserCourseController extends Controller
{
    public function index()
    {
        $registrations = Registration::with(['course', 'payment', 'user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('user.courses', compact('registrations'));
    }



    public function downloadCertificate(Certificate $certificate)
    {
        // dd($certificate->user_id);
        // Pastikan sertifikat milik pengguna yang sedang login
        if ($certificate->user_id !== auth()->id()) {
            abort(403);
        }

        // Logika untuk mengunduh sertifikat (misalnya, file PDF)
        $filePath = storage_path('app/public/certificates/sample-certificate.pdf'); // Ganti dengan path sebenarnya

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath);
    }
}
