<?php

namespace App\Http\Controllers;

use App\ApresiasiDetil;
use App\ApresiasiMhs;
use App\KrsTf;
use App\Mahasiswa;
use App\TrklklMf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NilaiApresiasiController extends Controller
{
    public function index()
    {
        $semuaApresiasiMhs = ApresiasiMhs::orderBy('id_apresiasi', 'desc')->get();

        return view('nilai-apresiasi.index', compact('semuaApresiasiMhs'));
    }

    public function create()
    {
        return view('nilai-apresiasi.create');
    }

    public function store(Request $request)
    {
        // NOTE: kenapa hanya bukti kegiatan yg divalidasi?
        // Karena itu berbentuk file, sehingga penanganan nya perlu sedikit extra
        // ketimbang data primitif lainnya, seperti string, int, array, dll.
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

        // Insert Apresiasi Mhs
        $apresiasiMhs = ApresiasiMhs::create([
            'smt'               => $request->smt,
            'nim'               => $request->nim,
            'jenis_kegiatan'    => $request->jenis_kegiatan,
            'prestasi_kegiatan' => $request->prestasi_kegiatan,
            'tingkat_kegiatan'  => $request->tingkat_kegiatan,
            'keterangan'        => $request->keterangan,
            'bukti_kegiatan'    => $filename,
        ]);

        // Insert Apresiasi Detil berdasarkan id_apresiasi dari Apresiasi Mhs
        foreach ($request->nilai_matkul as $nilai_matkul) {
            ApresiasiDetil::create([
                'id_apresiasi' => $apresiasiMhs->id_apresiasi,
                'klkl_id'      => $nilai_matkul['klkl_id'],
                'nilai'        => $nilai_matkul['nilai_angka'],
            ]);

            // NOTE: untuk KRS_TF dan JDWKUL, karena mereka tidak memiliki fillable, jadi untuk insert perhatikan kolom2 yang perlu diinsert
            // di method performInsert

            // Insert KRS_TF

            // Insert JDWKUL
        }




        return redirect()->route('nilaiapresiasi.index')->with('success', 'Nilai Apresiasi Mahasiswa berhasil ditambah.');
    }

    public function destroy(ApresiasiMhs $apresiasiMhs)
    {
        // Ambil semua Apresiasi Detil
        $apresiasiMhs = $apresiasiMhs->load('detil');

        // Hapus Apresiasi Detil
        $apresiasiMhs->detil->each(function ($apresiasiDetil) {
            $apresiasiDetil->delete();
        });

        // Hapus bukti kegiatan (klo ada)
        if ($apresiasiMhs->bukti_kegiatan) Storage::disk('bukti')->delete($apresiasiMhs->bukti_kegiatan);

        // Hapus Apresiasi Mhs
        $apresiasiMhs->delete();

        return back()->with('success', 'Nilai Apresiasi berhasil dihapus.');
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

        // Ambil semua klkl_id di ApresiasiDetil yang memiliki data di ApresiasiMhs dengan smt dan nim yang dipass
        $apresiasiDetil = ApresiasiDetil::query()
            ->whereHas('mahasiswa', function ($mahasiswa) use ($nim, $smt) {
                $mahasiswa->where('smt', $smt)->where('nim', $nim);
            })
            ->get()
            ->pluck('klkl_id');

        // Buang matkul yang sudah ada di apresiasiDetil
        $matkul = $matkul
            ->reject(function ($matkul) use ($apresiasiDetil) {
                return $apresiasiDetil->contains($matkul->klkl_id);
            })
            // Setelah di reject, index data perlu di reset ulang, agar dapat terbaca di javascript
            ->values();

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
