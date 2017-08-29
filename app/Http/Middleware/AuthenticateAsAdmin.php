<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class AuthenticateAsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! auth()->user()->isAdmin() )
        {
            if ( request()->wantsJson() )
                return response()->json([
                    'errors' => true, 
                    'message' => "You must be logged on as an admin to do that."
                ], 422);

            throw new AuthenticationException("You must be logged on as an administrator to do that.");
        }
            

        return $next($request);
    }
}
