<?php

namespace App\Http\Controllers;

use App\Models\ReadingSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = $request->user()
            ->readingSessions()
            ->orderBy('session_date', 'desc')
            ->get();

        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'book_title'       => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'session_date'     => 'required|date',
        ]);

        $session = $request->user()->readingSessions()->create($data);

        return response()->json($session, 201);
    }

    public function destroy(Request $request, ReadingSession $session)
    {
        if ($session->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $session->delete();
        return response()->json(['message' => 'Session deleted']);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        $now  = now();

        $totalMinutes = $user->readingSessions()->sum('duration_minutes');

        $weeklyBreakdown = $user->readingSessions()
            ->selectRaw("EXTRACT(DOW FROM session_date::date) as day, SUM(duration_minutes) as mins")
            ->whereBetween('session_date', [
                $now->copy()->startOfWeek()->toDateString(),
                $now->copy()->endOfWeek()->toDateString(),
            ])
            ->groupByRaw("EXTRACT(DOW FROM session_date::date)")
            ->orderByRaw("EXTRACT(DOW FROM session_date::date)")
            ->get();

        return response()->json([
            'total_books'      => $user->readingSessions()
                                    ->distinct('book_title')
                                    ->count('book_title'),
            'total_minutes'    => $totalMinutes,
            'total_hours'      => round($totalMinutes / 60, 1),
            'this_week'        => $user->readingSessions()
                                    ->whereBetween('session_date', [
                                        $now->copy()->startOfWeek()->toDateString(),
                                        $now->copy()->endOfWeek()->toDateString(),
                                    ])
                                    ->sum('duration_minutes'),
            'this_month'       => $user->readingSessions()
                                    ->whereMonth('session_date', $now->month)
                                    ->whereYear('session_date', $now->year)
                                    ->sum('duration_minutes'),
            'weekly_breakdown' => $weeklyBreakdown,
        ]);
    }
}