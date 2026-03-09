<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAccessTodayView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $authorizedEmails = [
            'lee@fitchysbarberlounge.co.uk',
            'me@jamiegee.co.uk'
        ];

        if (!in_array(auth()->user()->email, $authorizedEmails)) {
            abort(403, 'Unauthorized access to today view.');
        }

        return $next($request);
    }
}
