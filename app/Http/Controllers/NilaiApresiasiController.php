<?php

namespace App\Http\Controllers;

use App\ApresiasiDetil;
use App\ApresiasiMhs;
use App\Mahasiswa;
use App\TrklklMf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $request->validate([
            'bukti_kegiatan' => 'nullable|file|mimes:png,jpeg,pdf,doc,docx,xls,xlsx|max:12582912',
        ], [
            'bukti_kegiatan.mimes' => 'Bukti Kegiatan hanya boleh dalam bentuk PNG, JPEG, PDF, DOCX',
            'bukti_kegiatan.max' => 'Ukuran maksimal bukti kegiatan yang diijinkan adalah 10 MB',
        ]);


        $bukti_kegiatan = $request->file('bukti_kegiatan');
        $filename = null;

        if ($request->hasFile('bukti_kegiatan')) {
            $filename = $bukti_kegiatan->getClientOriginalName();

            Storage::disk('bukti')->putFileAs(null, $bukti_kegiatan, $filename);
        }

        $apresiasiMhs = ApresiasiMhs::create([
            'smt'               => $request->smt,
            'nim'               => $request->nim,
            'jenis_kegiatan'    => $request->jenis_kegiatan,
            'prestasi_kegiatan' => $request->prestasi_kegiatan,
            'tingkat_kegiatan'  => $request->tingkat_kegiatan,
            'keterangan'        => $request->keterangan,
            'bukti_kegiatan'    => $filename,
        ]);

        foreach ($request->nilai_matkul as $nilai_matkul) {
            ApresiasiDetil::create([
                'id_apresiasi' => $apresiasiMhs->id_apresiasi,
                'klkl_id'      => $nilai_matkul['klkl_id'],
                'nilai'        => $nilai_matkul['nilai_angka'],
            ]);
        }

        return redirect()->route('nilaiapresiasi.index')->with('success', 'Nilai Apresiasi Mahasiswa berhasil ditambah.');
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
                'sts_mk',
            ]);

        return response()->json([
            'matkul' => $matkul->count() ? $matkul : null,
        ]);
    }

    public function jsonGetNilaiHuruf($nilai_angka)
    {
        $nilai_huruf = DB::select("select nilai_huruf(?) AS nilai_huruf from dual", [$nilai_angka])[0]->nilai_huruf;

        return response()->json([
            'nilai_huruf' => $nilai_huruf
        ]);
    }
}
