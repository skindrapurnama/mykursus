<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\Registration;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registrations = Registration::all();
        foreach ($registrations as $reg) {
            if ($reg->status === 'approved') {
                Certificate::create([
                    'user_id' => $reg->user_id,
                    'course_id' => $reg->course_id,
                    'certificate_number' => 'CERT/' . now()->year . '/' . $reg->course_id . '/' . $reg->user_id,
                    'issued_at' => now(),
                ]);
            }
        }
    }
}
