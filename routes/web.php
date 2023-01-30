<?php

use Illuminate\Http\Request;
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

// NOTE: samakan kode rahasia pada aplikasi sumber (pada SIAKAD) dengan kode rahasia pada aplikasi ini
Route::get('login', function (Request $request) {
    // Hilangkan akses ke aplikasi
    session([
        'user_auth_allowance' => false,
        'user_auth_nik'       => null,
    ]);

    // Yang bisa akses aplikasi ini hanya Bu Sekar
    if ($request->nik != '970216') return 'Maaf, anda tidak memiliki hak akses untuk memasuki aplikasi ini';

    $secret_code = config('custom.secret_code');

    // Cek kombinasi secret code dan nik yang sudah di hash
    $cekLogin = Hash::check($secret_code . $request->nik, $request->pass);

    // Jika hash nya cocok/sama
    if ($cekLogin) {
        // Set User Session
        session([
            'user_auth_allowance' => true,
            'user_auth_nik'       => $request->nik,
        ]);

        // Teruskan ke aplikasi
        return redirect()->route('nilaiapresiasi.index');
    }

    // Kalau hash nya tidak cocok
    return 'Maaf, anda tidak memiliki hak akses untuk memasuki aplikasi ini';
});


// Jalur khusus developer, biar gk perlu buka SIAKAD
Route::get('/khusus_dev_ppti', function () {
    // Set User Session
    session([
        'user_auth_allowance' => true,
        'user_auth_nik'       => 'developer',
    ]);

    // Teruskan ke aplikasi
    return redirect()->route('nilaiapresiasi.index');
});



Route::middleware(['cek_kode'])->group(function () {
    Route::get('/', 'NilaiApresiasiController@index')->name('nilaiapresiasi.index');
    Route::get('/create', 'NilaiApresiasiController@create')->name('nilaiapresiasi.create');
    Route::get('/edit/{id_apresiasi}', 'NilaiApresiasiController@edit')->name('nilaiapresiasi.edit');
    Route::get('/cetak/{id_apresiasi}', 'NilaiApresiasiController@cetak')->name('nilaiapresiasi.cetak');
    Route::get('/cetak_new/{id_apresiasi}', 'NilaiApresiasiController@cetak_new')->name('nilaiapresiasi.cetak_new');
    Route::put('/update/{apresiasiMhs}', 'NilaiApresiasiController@update')->name('nilaiapresiasi.update');
    Route::post('/store', 'NilaiApresiasiController@store')->name('nilaiapresiasi.store');
    Route::delete('/destroy/{apresiasiMhs}', 'NilaiApresiasiController@destroy')->name('nilaiapresiasi.destroy');

    Route::get('/json/nama/mhs/{nim}', 'NilaiApresiasiController@jsonGetNamaMhs')->name('nilaiapresiasi.json.get.nama_mhs');
    Route::get('/json/matkul/mhs/{nim}/{smt}', 'NilaiApresiasiController@jsonGetMatkulMhs')->name('nilaiapresiasi.json.get.matkul_mhs');
    Route::get('/json/nilai_huruf/{nilai_angka}', 'NilaiApresiasiController@jsonGetNilaiHuruf')->name('nilaiapresiasi.json.get.nilai_huruf');
});
