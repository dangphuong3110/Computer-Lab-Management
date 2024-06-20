<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!auth()->check()) {
            session(['intended_url' => url()->current()]);
            return redirect('/login');
        }

        $user = Auth::user();
        $checkRole = Role::where('id', $user->role_id)->first();

        if ($checkRole->role_name != $role) {
            return redirect($checkRole->role_name);
        }

        return $next($request);
    }
}
