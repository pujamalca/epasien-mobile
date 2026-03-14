<?php

namespace App\Http\Middleware;

use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;

class EpasienSessionMiddleware
{
    public function __construct(private SessionService $session) {}

    public function handle(Request $request, Closure $next)
    {
        if (!$this->session->isLoggedIn()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'session_expired'], 401);
            }
            return redirect()->route('login');
        }
        return $next($request);
    }
}
