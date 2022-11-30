<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use App\TrklklMf;
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

    public function jsonGetMatkulMhs($nim, $smt)
    {
        $matkul = TrklklMf::query()
            ->where('mhs_nim', $nim)
            ->where('semester', $smt)
            ->with(['kurikulum' => function ($kurikulum) {
                $kurikulum->addSelect('id', 'nama', 'sks');
            }])
            ->get([
                'klkl_id',
                'mhs_nim',
                'semester',
            ]);

        return response()->json([
            'matkul' => $matkul->count() ? $matkul : null,
        ]);
    }
}
