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
        return view('members.create');
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
        
        if ($request->filled('telegram_id')) {
            $request->validate([
                'telegram_id' => 'required|string|max:20',
            ]);

            $user->update([
                'telegram_id' => $request->telegram_id,
            ]);

            return redirect()
                ->route('members.index')
                ->with('success', 'Telegram ID berhasil diperbarui.');
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
