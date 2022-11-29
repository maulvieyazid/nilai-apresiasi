<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use Illuminate\Http\Request;

class NilaiApresiasiController extends Controller
{
    public function index()
    {
        return view('nilai-apresiasi.index');
    }

    public function create()
    {
        return view('nilai-apresiasi.create');
    }

    public function store(Request $request)
    {
        dd($request->all());
    }


    public function jsonGetNamaMhs($nim)
    {
        $nama = Mahasiswa::where('nim', $nim)->first('nama');

        return response()->json($nama);
    }
}
