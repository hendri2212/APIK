<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m')); // Menggunakan bulan saat ini sebagai default

        $token = Session::get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post('https://gateway.apikv3.kalselprov.go.id/graphql', [
                "operationName" => null,
                "variables" => [
                    "tanggal" => $tanggal
                ],
                "query" => "query FetchEabsenPresenceHistory(\$tanggal: String!) { presensiEabsenHistoryMe(tanggal: \$tanggal) { tanggal_masuk tanggal_keluar jam_masuk jam_keluar status_code status_name nip jam_mulai_absen_pagi jam_mulai_absen_pulang jenis_jadwal jam_mulai_kerja jam_pulang_kerja lewathari id jam_keluar_status jam_masuk_status presensi_apik { presensi_id employee_nip presensi_tipe presensi_date presensi_time presensi_lat presensi_long presensi_status presensi_foto_url presensi_foto_file_name presensi_sync_eabsen presensi_sync_eabsen_id __typename } __typename } __typename }"
            ]);

            $data = $response->json()['data']['presensiEabsenHistoryMe'];

            return view('history.absent', compact('data', 'tanggal'));
        } catch (\Exception $e) {
            return view('history.absent', [
                'data' => [],
                'error' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage(),
            ]);
        }
    }
}
