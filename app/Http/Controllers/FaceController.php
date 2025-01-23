<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use App\Models\Face;

class FaceController extends Controller {
    private $userId; // Properti untuk menyimpan user_id

    public function __construct() {
        $this->middleware(function ($request, $next) {
            $this->userId = Session::get('user_id', 'default_value');
            return $next($request);
        });
    }

    public function index() {
        if (!$this->userId) {
            return redirect()->route('login')->withErrors(['authError' => 'Anda harus login terlebih dahulu.']);
        }

        $files = Face::where('user_id', $this->userId)->get();
        $daysOfWeek = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $facePaths = $files->map(function ($file) use ($daysOfWeek) {
            return [
                'id' => $file->id,
                'path' => route('face.show', ['file_name' => $file->face_name]),
                'day' => $daysOfWeek[$file->day] ?? 'Tidak Diketahui'
            ];
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

    public function store(Request $request) {
        // Validasi file
        $request->validate([
            'face_name' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Simpan file
        if ($request->hasFile('face_name')) {
            $file = $request->file('face_name');
            $filename = time() . '_' . $file->getClientOriginalName(); // Tambahkan timestamp
            $path = $file->storeAs('private/face', $filename);

            Face::create(['face_name' => $filename, 'user_id' => $this->userId]);

            // return back()->with('success', 'Image uploaded successfully!');
            return redirect('/face');
        }

        return back()->with('error', 'Please select a valid image.');
    }

    public function show($file_name) {
        $disk = Storage::disk('private');
        $filePath = "face/{$file_name}";

        if (!$disk->exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->file($disk->path($filePath));
        // return response()->download($disk->path($filePath));
    }

    public function edit($id) {
        $file = Face::findOrFail($id); // Ambil data berdasarkan ID
        return view('face.edit', compact('file')); // Kirim data ke view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {
        $request->validate([
            // 'face_name' => 'required|string|max:255',
            'day' => 'required|integer|between:0,6',
        ]);
    
        $file = Face::findOrFail($id);
        // $file->face_name = $request->input('face_name');
        $file->day = $request->input('day');
        $file->save(); // Simpan perubahan
    
        return redirect('/face')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Face $face)
    {
        //
    }
}
