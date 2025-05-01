<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Face;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class AutoCheckIn extends Command
{
    protected $signature = 'absen:auto-checkin';
    protected $description = 'Melakukan checkin otomatis untuk user dengan absent_type 1';

    public function handle()
    {
        $users = User::where('absent_type', 1)->get();

        foreach ($users as $user) {
            $this->checkinUser($user);
        }

        $this->info('Proses checkin otomatis selesai.');
    }

    // Paste method checkinUser() di sini (dari langkah 1 di atas)
    private function checkinUser($user)
    {
        // Method checkinUser di atas dimasukkan di sini.
        // Gunakan method yang sama seperti di atas.
    }

    private function generateRandomCoordinates($lat, $lng, $radius)
    {
        $radiusInDegrees = $radius / 111320; 
        $u = (float) rand() / (float) getrandmax();
        $v = (float) rand() / (float) getrandmax();
        $w = $radiusInDegrees * sqrt($u);
        $t = 2 * pi() * $v;
        $x = $w * cos($t);
        $y = $w * sin($t);

        $newLat = $lat + $y;
        $newLng = $lng + $x / cos($lat * (pi() / 180));

        return [$newLat, $newLng];
    }
}
