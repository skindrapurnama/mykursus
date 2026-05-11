<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $registrations = Registration::all();
        foreach ($registrations as $reg) {
            Payment::create([
                'registration_id' => $reg->id,
                'amount' => rand(500000, 1000000),
                'payment_method' => fake()->randomElement(['transfer', 'qris', 'cash', 'merchand']),
                'status' => fake()->randomElement(['pending', 'paid', 'rejected']),
                'paid_at' => now(),
            ]);
        }

        DB::statement("
        UPDATE registrations r
        JOIN payments p ON p.registration_id = r.id
        SET r.status =
            CASE
                WHEN p.status IN ('paid','approved') THEN 'approved'
                WHEN p.status = 'pending' THEN 'pending'
                ELSE p.status
            END
    ");
    }
}
