<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnforceJsonForApiRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //set the header to application/json for API route in-case merchant forget's to set their so response is always in json
        if ($request->is("api*")) {
            $request->headers->set('Accept', "application/json");
        }
        return $next($request);
    }
}
