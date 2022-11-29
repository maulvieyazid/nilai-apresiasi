<?php

namespace App\Http\Middleware;

// use App\SemesterMf;
use Closure;

class CheckSemester
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
        // WARNING : FAK_ID default '41010' atau jurusan SI, permintaan DBA
        // $semester = SemesterMf::find('41010');

        // Jika "semester yang akan datang" sama dengan "semester yang aktif", maka sudah tutup semester
        // Jika tidak sama, maka belum tutup semester
        // session(['isTutupSemester' => $semester->smt_yad == $semester->smt_aktif]);

        // return $next($request);
    }
}
