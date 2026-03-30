<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ReadingSession;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // All users with their session counts
    public function users()
    {
        $users = User::withCount('readingSessions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id'                    => $u->id,
                'name'                  => $u->name,
                'email'                 => $u->email,
                'role'                  => $u->role,
                'is_banned'             => $u->is_banned,
                'reading_sessions_count'=> $u->reading_sessions_count,
                'created_at'            => $u->created_at,
            ]);

        return response()->json($users);
    }

    // Platform-wide stats
    public function stats()
    {
        return response()->json([
            'total_users'    => User::where('role', '!=', 'super_admin')->count(),
            'total_sessions' => ReadingSession::count(),
            'total_minutes'  => ReadingSession::sum('duration_minutes'),
            'banned_users'   => User::where('is_banned', true)->count(),
        ]);
    }

    // Ban or unban a user
    public function toggleBan(Request $request, User $user)
    {
        if ($user->role === 'super_admin') {
            return response()->json(['message' => 'Cannot ban a super admin.'], 403);
        }

        $user->update(['is_banned' => !$user->is_banned]);

        return response()->json([
            'message'   => $user->is_banned ? 'User banned.' : 'User unbanned.',
            'is_banned' => $user->is_banned,
        ]);
    }

    // Permanently delete a user and all their data
    public function deleteUser(User $user)
    {
        if ($user->role === 'super_admin') {
            return response()->json(['message' => 'Cannot delete a super admin.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted.']);
    }
}