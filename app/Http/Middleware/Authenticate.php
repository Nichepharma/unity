<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }

        //Company Authontication
        $AuthCompany = Auth::user()->company;
        if ($AuthCompany !=0 && $request->is("*/{$AuthCompany}/*") == false && $request->is("*/{$AuthCompany}") == false){
          return "You're Nor Authorized To Visit Any Page Not Related To Your Company";
        }


        return $next($request);
    }
}
