<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('holiday_date')->get();

        return view('holiday.data', compact('holidays'));
    }

    /**
     * Toggle holiday flag for a given date.
     * POST the date to create; if it already exists, it will be removed.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'holiday_date' => 'required|date',
            'name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $existing = Holiday::whereDate('holiday_date', $data['holiday_date'])->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status' => 'removed',
                'date' => $data['holiday_date'],
            ]);
        }

        $holiday = Holiday::create([
            'holiday_date' => $data['holiday_date'],
            'name' => $data['name'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'added',
            'date' => $holiday->holiday_date,
        ]);
    }

    public function create()
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }

    public function destroy(string $id)
    {
        abort(404);
    }
}
