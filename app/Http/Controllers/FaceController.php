<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use App\Models\Face;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

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

        $files = Face::where('user_id', $this->userId)->orderBy('day', 'asc')->get();
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
        // Validasi file gambar
        $request->validate([
            'face_name' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        if ($request->hasFile('face_name')) {
            $file = $request->file('face_name');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Tentukan path penyimpanan secara private di storage
            $destinationPath = storage_path('app/private/face');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            try {
                // Resize gambar dengan mempertahankan aspect ratio
                $resizedImage = Image::read($file->getRealPath());
                $resizedImage->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Simpan gambar yang sudah diresize ke folder private
                $resizedImage->save($destinationPath . '/' . $filename);
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage());
            }

            // Simpan data ke database (pastikan user sudah login, atau gunakan cara lain untuk mendapatkan user_id)
            Face::create([
                'face_name' => $filename,
                'user_id'   => $this->userId,
                // 'user_id'   => Auth::id(),
            ]);

            return redirect('/face')->with('success', 'Image uploaded successfully!');
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
    public function destroy($id) {
        // Ambil data berdasarkan id, jika tidak ditemukan akan menghasilkan 404
        $face = Face::findOrFail($id);

        // Tentukan path lengkap file gambar yang tersimpan di folder storage/app/private/face
        // Misalnya nama file disimpan pada kolom 'image' di database
        $filePath = storage_path('app/private/face/' . $face->face_name);

        // Cek apakah file ada, lalu hapus file tersebut
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Hapus data record dari database
        $face->delete();

        // Redirect ke halaman index atau halaman lain dengan pesan sukses
        return redirect('/face')->with('success', 'Data dan file berhasil dihapus.');
    }
}
