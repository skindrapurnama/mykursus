<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Models\Course;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'courses' => Course::latest()->take(6)->get(),
        'testimonials' => Testimonial::with('user')->latest()->take(3)->get(),
    ]);
})->name('home');

Route::get('/courses/{course}', [CourseController::class, 'show'])
    ->name('courses.show');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/midtrans/callback', [App\Http\Controllers\MidtransController::class, 'callback']);

Route::post('/payment/{payment}/retry', [CourseController::class, 'retryPayment'])
    ->name('payment.retry');

Route::post('/simulate-payment/{payment}', function ($id) {
    $payment = \App\Models\Payment::findOrFail($id);
    $payment->update(['status' => 'approved']);
    return back();
});

Route::middleware('auth')->group(function () {

    Route::post('/courses/{course}/register', [CourseController::class, 'register'])
        ->name('courses.register');

    Route::post('/payment/{registration}', [CourseController::class, 'uploadPayment'])
        ->name('payment.upload');

    Route::get('/my-courses', [App\Http\Controllers\UserCourseController::class, 'index'])
        ->name('user.courses');

    Route::get('/certificate/{certificate}/download', [App\Http\Controllers\UserCourseController::class, 'downloadCertificate'])
        ->name('certificate.download');
});
