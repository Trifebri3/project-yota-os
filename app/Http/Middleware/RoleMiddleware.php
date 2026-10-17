<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If no specific roles are specified, check if active
        if ($user->status !== 'active') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is inactive.',
            ]);
        }

        // Read-only viewer role restriction
        if ($user->role === 'viewer') {
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete')) {
                return redirect()->back()->with('error', 'Akses ditolak: Hanya dapat membaca data (Read-Only Mode).');
            }
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect based on role if unauthorized
        if ($user->isSuperAdmin()) {
            return redirect('/super-admin/dashboard');
        } elseif ($user->isProgramAdmin() || $user->isProjectOfficer() || $user->isSupervisor()) {
            return redirect('/program-admin/dashboard');
        } else {
            return redirect('/volunteer/dashboard');
        }
    }
}
