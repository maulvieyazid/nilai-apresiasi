<?php

namespace App\Http\Middleware;

use Closure;

class CheckKodeRahasia
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
        // Ambil nilai session "user_auth_allowance"
        $ijin = session('user_auth_allowance');

        // Kalau nilainya true, maka lanjutkan request
        if (!!$ijin) {
            return $next($request);
        }

        // Kalau false, maka return 401
        return response('Maaf, anda tidak memiliki hak akses untuk memasuki aplikasi ini', 401);
    }
}
