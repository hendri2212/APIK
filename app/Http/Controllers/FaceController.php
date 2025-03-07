<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;
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
        // Validasi file gambar menggunakan Validator dengan custom message
        $validator = Validator::make($request->all(), [
            'face_name' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            'face_name.required' => 'Gambar harus diupload.',
            'face_name.image'    => 'File harus berupa gambar.',
            'face_name.mimes'    => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'face_name.max'      => 'Ukuran gambar maksimal 10MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Pastikan file ada di request
        if ($request->hasFile('face_name')) {
            $file = $request->file('face_name');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Tentukan path penyimpanan secara private di storage
            $destinationPath = storage_path('app/private/face');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            try {
                // (Opsional) Atur batas memori jika diperlukan
                // ini_set('memory_limit', '256M');

                // Resize gambar dengan mempertahankan aspect ratio
                $image = Image::read($file->getRealPath());

                // Perbaiki orientasi gambar jika memiliki metadata EXIF
                if (method_exists($image, 'orientate')) {
                    $image->orientate();
                } else {
                    // Tangani orientasi secara manual jika memungkinkan
                    if (function_exists('exif_read_data')) {
                        dd("Fungsi aktif");
                    } else {
                        dd("Fungsi tidak aktif");
                    }
                    //     $exif = @exif_read_data($file->getRealPath());
                    //     if ($exif && isset($exif['Orientation'])) {
                    //         switch ($exif['Orientation']) {
                    //             case 3:
                    //                 $image->rotate(180);
                    //                 break;
                    //             case 6:
                    //                 $image->rotate(-90);
                    //                 break;
                    //             case 8:
                    //                 $image->rotate(90);
                    //                 break;
                    //         }
                    //     }
                    // }
                }

                // Tentukan lebar target
                $targetWidth = 800;
                // Hitung tinggi baru berdasarkan aspek rasio asli
                $targetHeight = intval(($image->height() / $image->width()) * $targetWidth);

                // Resize gambar secara proporsional
                $image->resize($targetWidth, $targetHeight, function($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Simpan gambar yang sudah diresize ke folder private
                $image->save($destinationPath . '/' . $filename);
            } catch (\Exception $e) {
                return back()->with('error', 'Terjadi kesalahan saat mengupload gambar: ' . $e->getMessage());
            }

            // Simpan data ke database (pastikan user sudah login, atau gunakan cara lain untuk mendapatkan user_id)
            Face::create([
                'face_name' => $filename,
                'user_id'   => $this->userId,
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
