<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JamAbsen;

class JamAbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $jamAbsen = JamAbsen::first();
        return view('schedule.index', compact('jamAbsen'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        $request->validate([
            'checkin_time' => 'required',
            'checkout_time' => 'required',
        ]);

        $jamAbsen = JamAbsen::first();
        if (!$jamAbsen) {
            $jamAbsen = new JamAbsen();
        }

        $jamAbsen->checkin_time = $request->checkin_time;
        $jamAbsen->checkout_time = $request->checkout_time;
        $jamAbsen->save();

        return redirect()->back()->with('success', 'Jam absen berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
