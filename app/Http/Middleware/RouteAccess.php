<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouteAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $pathNoSlash = trim($request->path(), '/');
        $pathWithSlash = '/' . $pathNoSlash;

        $submodule = DB::table('submodules')
            ->where('status', 1)
            ->where(function ($query) use ($pathNoSlash, $pathWithSlash) {
                $query->where('links', $pathNoSlash)
                    ->orWhere('links', $pathWithSlash)
                    ->orWhereRaw("TRIM(BOTH '/' FROM links) = ?", [$pathNoSlash]);
            })
            ->select('id')
            ->first();

        // Only enforce where an active submodule-link exists.
        if (!$submodule) {
            return $next($request);
        }

        $hasAccess = DB::table('assign_role_modules')
            ->where('roleid', $user->userrole)
            ->where('submoduleid', $submodule->id)
            ->exists();

        if (!$hasAccess) {
            return redirect('/')->with('error_message', 'Access denied. Your role is not assigned to this route.');
        }

        return $next($request);
    }
}

