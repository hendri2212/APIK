<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamAbsenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('jam_absens')->insert([
            'checkin_time'  => '07:20:00',
            'checkout_time' => '16:30:00',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
