<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Only redirect if user is trying to access login page
                if ($request->routeIs('login')) {
                    $user = Auth::guard($guard)->user();
                    if ($user) {
                        switch ($user->role) {
                            case 'admin':
                                return redirect(RouteServiceProvider::ADMIN_HOME);
                            case 'kepala_sekolah':
                                return redirect(RouteServiceProvider::KEPALA_HOME);
                            case 'guru': // Fallthrough
                            case 'staff':
                                return redirect(RouteServiceProvider::PEGAWAI_HOME);
                            default:
                                return redirect(RouteServiceProvider::HOME);
                        }
                    }
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
} 