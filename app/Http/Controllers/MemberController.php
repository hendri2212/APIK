<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

class MemberController extends Controller {
    public function index() {
        $members = User::all();
        return view('members.index', compact('members'));
    }

    public function create() {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|max:255|unique:users,username',
            'password'    => 'required|string',
            'telegram_id' => 'required|string|max:20',
            'expired'     => 'required|date',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->password = $request->password;
        $user->telegram_id = $request->telegram_id;
        $user->expired = $request->expired;
        $user->uuid = Str::random(16);

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
        $request->validate([
            'absent_type' => 'required|in:0,1',
        ]);

        $user->update(['absent_type' => $request->absent_type]);

        return redirect()
            ->back()
            ->with('success', 'Absent type berhasil di-toggle menjadi ' . $request->input('absent_type') . '.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
