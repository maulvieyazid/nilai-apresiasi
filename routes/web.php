<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Karna aplikasi ini tidak memiliki halaman login, sehingga untuk membatasi akses
// Yaitu dengan menggabungkan kode rahasia dan nik user yang di hash
// Kemudian cek hasil hash nya, jika sama, berarti user memiliki hak untuk masuk ke aplikasi

// NOTE: samakan kode rahasia pada aplikasi sumber (pada SIAKAD) dengan kode rahasisa pada aplikasi ini
Route::get('login', function (Request $request) {
    $secret_code = config('custom.secret_code');

    // Cek kombinasi secret code dan nik yang sudah di hash
    $cekLogin = Hash::check($secret_code . $request->nik, $request->pass);

    // Jika hash nya cocok/sama
    if ($cekLogin) {
        // Set Auth Login / Set User Session
        // $karyawan = Karyawan::findOrFail($request->nik);
        // Auth::login($karyawan);

        // Teruskan ke aplikasi
        return redirect()->route('nilaiapresiasi.index');
    }

    return 'Maaf, anda tidak memiliki hak akses untuk memasuki aplikasi ini';
});

// Nantinya masukkan route2 aplikasi ke dalam group middleware auth, kecuali route login
// Middleware auth dapat digunakan karna saat login berhasil, auth user langsung diset
Route::middleware(['auth'])->group(function () {
});

Route::get('/', 'NilaiApresiasiController@index')->name('nilaiapresiasi.index');
Route::get('/create', 'NilaiApresiasiController@create')->name('nilaiapresiasi.create');
Route::post('/store', 'NilaiApresiasiController@store')->name('nilaiapresiasi.store');

Route::get('/json/nama/mhs/{nim}', 'NilaiApresiasiController@jsonGetNamaMhs')->name('nilaiapresiasi.json.get.nama_mhs');
Route::get('/json/matkul/mhs/{nim}/{smt}', 'NilaiApresiasiController@jsonGetMatkulMhs')->name('nilaiapresiasi.json.get.matkul_mhs');
Route::get('/json/nilai_huruf/{nilai_angka}', 'NilaiApresiasiController@jsonGetNilaiHuruf')->name('nilaiapresiasi.json.get.nilai_huruf');
