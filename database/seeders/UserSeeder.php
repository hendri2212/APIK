<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Hendri Arifin, S.Kom',
                'username' => 'cowok_cool320@yahoo.co.id',
                'password' => 'hendri2212',
                'uuid' => '72933c6ac7954f61',
                // 'office_id' => 1, // Sesuaikan dengan ID yang ada di tabel offices
                // 'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mohammad Alifan, S.Kom',
                'username' => '199011042022211001',
                'password' => 'J91rem4G',
                'uuid' => 'c642ecbe9f666780',
                // 'office_id' => 2, // Sesuaikan dengan ID yang ada di tabel offices
                // 'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
