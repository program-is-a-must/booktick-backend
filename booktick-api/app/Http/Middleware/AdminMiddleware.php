<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
// database/migrations/xxxx_add_role_to_users_table.php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}