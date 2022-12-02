<?php

namespace App\Http\Controllers;

use App\ApresiasiDetil;
use App\ApresiasiMhs;
use App\Jdwkul;
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

        // Kalo ada bukti kegiatannya, maka simpan file nya ke disk bukti
        // untuk mengecek konfigurasi disk, buka config/filesystems.php
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

            // NOTE: untuk KRS_TF dan JDWKUL, karena mereka tidak memiliki fillable, jadi untuk insert perhatikan attribut2 yang perlu diinsert
            // di method performInsert

            // Insert KRS_TF
            KrsTf::create([
                'jkul_klkl_id' => $nilai_matkul['klkl_id'],
                'mhs_nim'      => $request->nim,
                'nilai_akhir'  => $nilai_matkul['nilai_angka'], // <- Hati2, key dan value nya berbeda, key nya 'nilai_akhir', sedangkan value nya 'nilai_angka'
                'nilai_huruf'  => $nilai_matkul['nilai_huruf'],
                'sts_mk'       => $nilai_matkul['sts_mk'],
            ]);

            // Insert JDWKUL
            Jdwkul::create([
                'mhs_nim' => $request->nim,
                'klkl_id' => $nilai_matkul['klkl_id'],
                'sks'     => $nilai_matkul['sks'],
            ]);
        }

        return redirect()->route('nilaiapresiasi.index')->with('success', 'Nilai Apresiasi Mahasiswa berhasil ditambah.');
    }


    public function edit($id_apresiasi)
    {
        $apresiasiMhs = ApresiasiMhs::query()
            ->with(['detil', 'mhs'])
            ->where('id_apresiasi', $id_apresiasi)
            ->firstOrFail();

        // Karena ada data2 yang hanya tersimpan di KrsTf, jadi terpaksa mengambil data dari KrsTf
        // Selain itu, Apresiasi Detil tidak bisa di eager loading dengan KrsTf karena memerlukan beberapa key untuk disambungkan
        $semuaKrs = KrsTf::query()
            ->where('jkul_kelas', KrsTf::DEFAULT_JKUL_KELAS)
            ->whereIn('jkul_klkl_id', $apresiasiMhs->detil->pluck('klkl_id'))
            ->where('mhs_nim', $apresiasiMhs->nim)
            ->with(['kurikulum' => function ($kurikulum) {
                $kurikulum->addSelect(['id', 'nama', 'sks']);
            }])
            ->get();

        return view('nilai-apresiasi.edit', compact('apresiasiMhs', 'semuaKrs'));
    }

    public function update(Request $request, ApresiasiMhs $apresiasiMhs)
    {
        $bukti_kegiatan = $request->file('bukti_kegiatan');
        $filename = $apresiasiMhs->bukti_kegiatan;

        // Kalo ada bukti kegiatannya, maka hapus bukti yang sebelumnya lalu simpan bukti yg baru
        if ($request->hasFile('bukti_kegiatan')) {
            $filename = $bukti_kegiatan->getClientOriginalName();

            Storage::disk('bukti')->delete($apresiasiMhs->bukti_kegiatan);

            Storage::disk('bukti')->putFileAs(null, $bukti_kegiatan, $filename);
        }

        // Update Apresiasi Mhs
        $apresiasiMhs->update([
            'jenis_kegiatan'    => $request->jenis_kegiatan,
            'prestasi_kegiatan' => $request->prestasi_kegiatan,
            'tingkat_kegiatan'  => $request->tingkat_kegiatan,
            'keterangan'        => $request->keterangan,
            'bukti_kegiatan'    => $filename,
        ]);

        foreach ($request->nilai_matkul as $nilai_matkul) {
            // Update Krs Tf dengan cara hapus dulu datanya, lalu diinsert ulang
            $krs = new KrsTf([
                'jkul_klkl_id' => $nilai_matkul['klkl_id'],
                'mhs_nim'      => $apresiasiMhs->nim,
            ]);

            $krs->exists = true;

            // Hapus KRS_TF
            $krs->delete();

            // Buat lagi dengan value yang baru
            KrsTf::create([
                'jkul_klkl_id' => $nilai_matkul['klkl_id'],
                'mhs_nim'      => $apresiasiMhs->nim,
                'nilai_akhir'  => $nilai_matkul['nilai_angka'], // <- Hati2, key dan value nya berbeda, key nya 'nilai_akhir', sedangkan value nya 'nilai_angka'
                'nilai_huruf'  => $nilai_matkul['nilai_huruf'],
                'sts_mk'       => $nilai_matkul['sts_mk'],
            ]);


            // Update Apresiasi Detil
            $apresiasiDetil = ApresiasiDetil::query()
                ->where('id_apresiasi', $apresiasiMhs->id_apresiasi)
                ->where('klkl_id', $nilai_matkul['klkl_id'])
                ->first();

            $apresiasiDetil->update([
                'nilai' => $nilai_matkul['nilai_angka']
            ]);
        }

        return redirect()->route('nilaiapresiasi.index')->with('success', 'Nilai Apresiasi Mahasiswa berhasil diupdate.');
    }


    public function destroy(ApresiasiMhs $apresiasiMhs)
    {
        // Ambil semua Apresiasi Detil
        $apresiasiMhs = $apresiasiMhs->load('detil');

        // Looping apresiasi detil
        $apresiasiMhs->detil->each(function ($apresiasiDetil) use ($apresiasiMhs) {

            // NOTE: untuk KRS_TF dan JDWKUL, tidak perlu mengambil data ke DB menggunakan get() ataupun first(),
            // melainkan langsung isikan attribute nya ke instance nya

            // Isi attribute KRS_TF
            $krstf = new KrsTf([
                'jkul_klkl_id' => $apresiasiDetil->klkl_id,
                'mhs_nim'      => $apresiasiMhs->nim,
            ]);

            // NOTE: bila instance model tidak didapatkan dari DB, maka attribute "exists" akan bernilai false,
            // Sedangkan di method delete(), bila "exists" ini bernilai false, maka dia akan langsung return,
            // dan tidak akan menjalankan method performDeleteOnModel(), maka dari itu attribute ini perlu di set ke true
            $krstf->exists = true;

            // Hapus KRS_TF
            $krstf->delete();

            // Isi attribute Jdwkul
            $jdwkul = new Jdwkul([
                'klkl_id' => $apresiasiDetil->klkl_id,
                'prodi'   => substr($apresiasiMhs->nim, 2, 5),
            ]);

            $jdwkul->exists = true;

            // Untuk Jdwkul, sebelum menghapus, perlu dicek dulu,
            // apakah ada apresiasi detil lain yang klkl_id nya sama tetapi bukan apresiasi detil yang sedang di looping saat ini?
            // Kalo ada, maka jangan hapus Jdwkul, Kalo enggak ada, maka hapus Jdwkul
            $hasSameKlklid = ApresiasiDetil::query()
                ->where('klkl_id', $apresiasiDetil->klkl_id)
                ->where('id_apresiasi', '!=', $apresiasiDetil->id_apresiasi)
                ->count();

            // Hapus Jdwkul
            if (!$hasSameKlklid) $jdwkul->delete();

            // Hapus Apresiasi Detil
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
