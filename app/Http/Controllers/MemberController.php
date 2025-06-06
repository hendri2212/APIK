<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class MemberController extends Controller {
    public function index() {
        $members = User::all();
        return view('members.index', compact('members'));
    }

    public function create() {
        return view('members.add');
    }

    public function store(Request $request) {
        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:255|unique:users,username',
            'password'    => 'required|string',
            'uuid'        => 'required|string',
            'telegram_id' => 'required|string|max:20',
            'expired'     => 'required|date',
        ]);

        $user = new User();
        $user->name         = $request->name;
        $user->username     = $request->username;
        $user->password     = $request->password;
        $user->uuid         = $request->uuid;
        $user->telegram_id  = $request->telegram_id;
        $user->expired      = $request->expired;

        $user->save();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id) {
        return view('members.edit', [
            'member' => User::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $id) {
        $user = User::findOrFail($id);
        
        // Cek apakah request dari form edit member (berdasarkan field yang ada)
        if ($request->has(['name', 'username', 'uuid', 'telegram_id', 'expired'])) {
            // Validasi untuk update member data
            $validationRules = [
                'name'        => 'required|string|max:255',
                'username'    => 'required|string|max:255|unique:users,username,' . $id,
                'uuid'        => 'required|string',
                'telegram_id' => 'required|string|max:20',
                'expired'     => 'required|date',
            ];
            
            // Password optional saat update (hanya validasi jika diisi)
            if ($request->filled('password')) {
                $validationRules['password'] = 'required|string|min:6';
            }
            
            $request->validate($validationRules);

            // Data yang akan diupdate
            $updateData = [
                'name'        => $request->name,
                'username'    => $request->username,
                'uuid'        => $request->uuid,
                'telegram_id' => $request->telegram_id,
                'expired'     => $request->expired,
            ];
            
            // Simpan password tanpa hash jika diisi
            if ($request->filled('password')) {
                $updateData['password'] = $request->password;
            }

            $user->update($updateData);

            return redirect()
                ->route('members.index')
                ->with('success', 'Data member berhasil diperbarui.');
        }
        
        // Penanganan untuk update telegram_id dan expired saja (backward compatibility)
        if ($request->filled('telegram_id') || $request->filled('expired')) {
            $request->validate([
                'telegram_id' => 'required|string|max:20',
                'expired' => 'required|date',
            ]);

            $user->update([
                'telegram_id' => $request->telegram_id,
                'expired' => $request->expired,
            ]);

            return redirect()
                ->route('members.index')
                ->with('success', 'Data member berhasil diperbarui.');
        }

        // Penanganan default untuk switch absent_type
        if ($request->has('absent_type')) {
            $request->validate([
                'absent_type' => 'required|in:0,1',
            ]);

            $user->update(['absent_type' => $request->absent_type]);

            return redirect()
                ->back()
                ->with('success', 'Absent type berhasil di-toggle menjadi ' . $request->input('absent_type') . '.');
        }

        return redirect()
            ->back()
            ->with('error', 'Tidak ada data yang diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}