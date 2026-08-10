<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardBasicAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->getUser();
        $pass = $request->getPassword();

        $validUser = $user !== null && hash_equals((string) config('services.dashboard.user'), (string) $user);
        $validPass = $pass !== null && hash_equals((string) config('services.dashboard.pass'), (string) $pass);

        if (!$validUser || !$validPass) {
            return response('Unauthorized', 401)
                ->header('WWW-Authenticate', 'Basic realm="Dashboard"');
        }

        return $next($request);
    }
}