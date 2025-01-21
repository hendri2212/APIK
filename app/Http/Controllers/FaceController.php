<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Face;

class FaceController extends Controller {
    public function index() {
        $userId = Session::get('user_id'); // Ambil user_id dari session
        if (!$userId) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        // Dapatkan semua nama file dari model Face
        $files = Face::where('user_id', $userId)->pluck('face_name'); // Misalnya ada kolom `face_name`

        // Konversi menjadi path lengkap
        $facePaths = $files->map(function ($fileName) {
            return route('face.show', ['face' => $fileName]);
        });

        return view('face.data', compact('facePaths'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Face $face) {
        $filePath = storage_path('app/private/face/{$face->id}');

        // Periksa apakah file ada
        if (!Storage::exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->file(Storage::path($filePath));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Face $face)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Face $face)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Face $face)
    {
        //
    }
}
