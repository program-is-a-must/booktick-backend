<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = Challenge::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($challenges);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'daily_minutes' => 'required|integer|min:1',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after:start_date',
            'is_active'     => 'boolean',
        ]);

        $challenge = Challenge::create($data);
        return response()->json($challenge, 201);
    }

    public function update(Request $request, Challenge $challenge)
    {
        $data = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'daily_minutes' => 'sometimes|integer|min:1',
            'start_date'    => 'sometimes|date',
            'end_date'      => 'sometimes|date',
            'is_active'     => 'sometimes|boolean',
        ]);

        $challenge->update($data);
        return response()->json($challenge);
    }

    public function destroy(Challenge $challenge)
{
    $challenge->delete();
    return response()->json(['message' => 'Challenge deleted']);
}
}