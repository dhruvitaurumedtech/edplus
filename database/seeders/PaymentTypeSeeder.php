<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            ['name' => 'cheque'],
            ['name' => 'card'],
            ['name' => 'net banking'],
            ['name' => 'cash'],
            ['name' => 'upi'],
        ];

        DB::table('payment_type')->insert($paymentTypes);
    }
}
