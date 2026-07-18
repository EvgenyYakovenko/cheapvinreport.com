<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->to(\App\Support\LocaleRoute::route('login'));
        }

        $user = Auth::user();

        if ($user->role === $role || $user->role === 'admin') {
            return $next($request);
        } else {
            return redirect()->to(\App\Support\LocaleRoute::route('index'));
        }
    }
}
