<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbleFinishOrder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        // return response($user);
        if($user->role_id != 2 && $user->role_id != 4){
            // abort(403, 'you cannot access this function');
            return response('you cannot access this function',403);
        }
        return $next($request);
    }
}
