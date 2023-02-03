<?php

namespace App\Http\Controllers;

use App\ApresiasiDetil;
use App\ApresiasiMhs;
use App\KrsLama;
use App\KrsTf;
use App\Mahasiswa;
use App\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class NilaiApresiasiController extends Controller
{
    public function index()
    {
        $semuaApresiasiMhs = ApresiasiMhs::with('mhs')->orderBy('id_apresiasi', 'desc')->get();

        return view('nilai-apresiasi.index', compact('semuaApresiasiMhs'));
    }

    public function create()
    {
        $smt = Semester::find('41010');

        return view('nilai-apresiasi.create', compact('smt'));
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
        foreach ($request->nilai_matkul as $matkul) {
            ApresiasiDetil::create([
                'id_apresiasi'     => $apresiasiMhs->id_apresiasi,
                'klkl_id'          => $matkul['klkl_id'],
                'nilai'            => $matkul['nilai_angka'],
                'persen_kehadiran' => $matkul['pro_hdr'],
                'sts_presensi'     => $matkul['sts_pre'],
                'uas_lama'         => $matkul['n_uas'],
            ]);

            // Insert Krs Lama
            KrsLama::create([
                'mhs_nim'      => $request->nim,
                'jkul_klkl_id' => $matkul['klkl_id'],
                'jkul_kelas'   => $matkul['jkul_kelas'],
            ]);

            // Update KRS_TF
            // NOTE : update dan insert sama2 memanggil fungsi save(), tetapi agar bisa menjalankan method performUpdate(),
            // maka atribut "exists" harus bernilai true, bila tidak, maka akan menjalankan method performInsert().
            $krs = new KrsTf([
                'mhs_nim'      => $request->nim,
                'jkul_klkl_id' => $matkul['klkl_id'],
                'jkul_kelas'   => $matkul['jkul_kelas'],
                'nilai'        => $matkul['nilai_angka'],
                'jenis'        => KrsTf::JENIS_SIMPAN,
            ]);
            $krs->exists = true;
            $krs->save();
        }

        return redirect()->route('nilaiapresiasi.index')->with('success', "Nilai Apresiasi Mahasiswa berhasil ditambah.");
    }


    public function edit($id_apresiasi)
    {
        // Mengambil Apresiasi Mhs yang id_apresiasi nya sama dengan parameter $id_apresiasi
        $apresiasiMhs = ApresiasiMhs::query()
            ->with(['detil', 'mhs'])
            ->where('id_apresiasi', $id_apresiasi)
            ->firstOrFail();

        // Mengambil Apresiasi Detail dari nim yang sama, tetapi id_apresiasi nya tidak sama dengan parameter $id_apresiasi
        // Dengan kata lain, mengambil matkul2 pada Apresiasi Detil dari mahasiswa yang sama, tapi yang sudah dipakai di kegiatan yang lain
        $apresiasiDetilLain = ApresiasiDetil::query()
            ->whereHas('mahasiswa', function ($mahasiswa) use ($apresiasiMhs) {
                $mahasiswa
                    ->where('nim', $apresiasiMhs->nim)
                    ->where('id_apresiasi', '!=', $apresiasiMhs->id_apresiasi);
            })
            ->get();

        // Ambil KRS_TF yang nim nya sesuai dengan $apresiasiMhs diatas
        // DAN jkul_klkl_id nya tidak ada di $apresiasiDetilLain
        $semuaKrs = KrsTf::query()
            ->where('mhs_nim', $apresiasiMhs->nim)
            ->whereNotIn('jkul_klkl_id', $apresiasiDetilLain->pluck('klkl_id'))
            ->with(['kurikulum' => function ($kurikulum) {
                $kurikulum->addSelect('id', 'nama', 'sks');
            }])
            ->orderBy('jkul_klkl_id')
            ->orderBy('jkul_kelas')
            ->get([
                'jkul_klkl_id',
                'jkul_kelas',
                'mhs_nim',
                'sts_mk',
                'n_uas',
            ]);

        // Menambahkan atribut "centang" pada matkul yang masuk ke dalam detil $apresiasiMhs
        $semuaKrs->transform(function ($krs) use ($apresiasiMhs) {
            $detilApresiasi = $apresiasiMhs->detil->firstWhere('klkl_id', $krs->jkul_klkl_id);

            // Jika krs ini, masuk ke dalam detil $apresiasiMhs
            if (!!$detilApresiasi) {
                $krs->centang = true;
                $krs->nilai_apr_detil = $detilApresiasi->nilai;
            }

            return $krs;
        });

        return view('nilai-apresiasi.edit', compact('apresiasiMhs', 'semuaKrs'));
    }

    public function update(Request $request, ApresiasiMhs $apresiasiMhs)
    {
        $apresiasiMhs = $apresiasiMhs->load('detil');

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


        // Untuk update Nilai Apresiasi, dilakukan dua tahap pemrosesan
        // - Tahap pertama yaitu removing, pada tahap ini yg dilakukan sama seperti method destroy, yaitu Update KrsTf lalu Hapus Krs Lama lalu Hapus Apresiasi Detil
        // - Tahap kedua yaitu inserting, pada tahap ini juga sama seperti method create, yaitu insert Apresiasi Detil, Insert Krs Lama, lalu Update KrsTF

        // TAHAP PERTAMA : Removing
        foreach ($apresiasiMhs->detil as $apresiasiDetil) {
            // Ambil data dari KRS_TF untuk mengambil nilai jkul_kelas nya saja
            $dataKrs = KrsTf::query()
                ->where('mhs_nim', $apresiasiMhs->nim)
                ->where('jkul_klkl_id', $apresiasiDetil->klkl_id)
                ->first();

            // Update KrsTF
            // NOTE : update dan insert sama2 memanggil fungsi save(), tetapi agar bisa menjalankan method performUpdate(),
            // maka atribut "exists" harus bernilai true, bila tidak, maka akan menjalankan method performInsert().
            $krs = new KrsTf([
                'mhs_nim'      => $apresiasiMhs->nim,
                'jkul_klkl_id' => $apresiasiDetil->klkl_id,
                'jkul_kelas'   => $dataKrs->jkul_kelas,
                'nilai'        => null,
                'jenis'        => KrsTf::JENIS_HAPUS,
            ]);
            $krs->exists = true;
            $krs->save();

            // Hapus Krs Lama
            // NOTE: untuk menghapus Krs Lama, tidak perlu mengambil data ke DB menggunakan get() ataupun first(),
            // melainkan langsung isikan attribute nya ke instance nya
            $krsLama = new KrsLama([
                'mhs_nim'      => $apresiasiMhs->nim,
                'jkul_klkl_id' => $apresiasiDetil->klkl_id,
                'jkul_kelas'   => $dataKrs->jkul_kelas,
            ]);
            // NOTE: bila instance model tidak didapatkan dari DB, maka attribute "exists" akan bernilai false,
            // Sedangkan di method delete(), bila "exists" ini bernilai false, maka dia akan langsung return,
            // dan tidak akan menjalankan method performDeleteOnModel(), maka dari itu attribute ini perlu di set ke true
            $krsLama->exists = true;
            $krsLama->delete();

            // Hapus ApresiasiDetil
            $apresiasiDetil->delete();
        }

        // TAHAP KEDUA : Inserting
        foreach ($request->nilai_matkul as $matkul) {

            // Sebelum mengupdate data matkul, cek dulu apakah matkul nya sudah dipakai di kegiatan lain atau belum
            $inApresiasiLain = ApresiasiDetil::query()
                ->where('klkl_id', $matkul['klkl_id'])
                ->whereHas('mahasiswa', function ($mahasiswa) use ($apresiasiMhs) {
                    $mahasiswa
                        ->where('nim', $apresiasiMhs->nim)
                        ->where('id_apresiasi', '!=', $apresiasiMhs->id_apresiasi);
                })
                ->count();

            // Kalau matkul nya ada di kegiatan lain, maka skip ke matkul selanjutnya
            if (!!$inApresiasiLain) continue;

            // Ambil data KRS sebelum di Update
            // ini untuk mengambil nilai PRO_HDR, STS_PRE, dan N_UAS untuk dimasukkan ke ApresiasiDetil yang baru
            $krsBeforeUpdate = KrsTf::query()
                ->where('mhs_nim', $apresiasiMhs->nim)
                ->where('jkul_klkl_id', $matkul['klkl_id'])
                ->first();

            // Insert ApresiasiDetil baru
            ApresiasiDetil::create([
                'id_apresiasi'     => $apresiasiMhs->id_apresiasi,
                'klkl_id'          => $matkul['klkl_id'],
                'nilai'            => $matkul['nilai_angka'],
                'persen_kehadiran' => $krsBeforeUpdate->pro_hdr,
                'sts_presensi'     => $krsBeforeUpdate->sts_pre,
                'uas_lama'         => $krsBeforeUpdate->n_uas,
            ]);

            // Insert Krs Lama
            KrsLama::create([
                'mhs_nim'      => $apresiasiMhs->nim,
                'jkul_klkl_id' => $matkul['klkl_id'],
                'jkul_kelas'   => $matkul['jkul_kelas'],
            ]);

            // Update KRS_TF
            // NOTE : update dan insert sama2 memanggil fungsi save(), tetapi agar bisa menjalankan method performUpdate(),
            // maka atribut "exists" harus bernilai true, bila tidak, maka akan menjalankan method performInsert().
            $krs = new KrsTf([
                'mhs_nim'      => $apresiasiMhs->nim,
                'jkul_klkl_id' => $matkul['klkl_id'],
                'jkul_kelas'   => $matkul['jkul_kelas'],
                'nilai'        => $matkul['nilai_angka'],
                'jenis'        => KrsTf::JENIS_SIMPAN,
            ]);
            $krs->exists = true;
            $krs->save();
        }

        return back()->with('success', "Nilai Apresiasi Mahasiswa berhasil diupdate.");
    }


    public function destroy(ApresiasiMhs $apresiasiMhs)
    {
        // Ambil semua Apresiasi Detil
        $apresiasiMhs = $apresiasiMhs->load('detil');

        // Looping apresiasi detil
        foreach ($apresiasiMhs->detil as $apresiasiDetil) {

            // Ambil data dari KRS_TF untuk mengambil nilai jkul_kelas nya saja
            $dataKrs = KrsTf::query()
                ->where('mhs_nim', $apresiasiMhs->nim)
                ->where('jkul_klkl_id', $apresiasiDetil->klkl_id)
                ->first();

            // Update KRS_TF
            // NOTE : update dan insert sama2 memanggil fungsi save(), tetapi agar bisa menjalankan method performUpdate(),
            // maka atribut "exists" harus bernilai true, bila tidak, maka akan menjalankan method performInsert().
            $krs = new KrsTf([
                'mhs_nim'      => $apresiasiMhs->nim,
                'jkul_klkl_id' => $apresiasiDetil->klkl_id,
                'jkul_kelas'   => $dataKrs->jkul_kelas,
                'nilai'        => null,
                'jenis'        => KrsTf::JENIS_HAPUS,
            ]);
            $krs->exists = true;
            $krs->save();

            // Hapus Krs Lama
            // NOTE: untuk menghapus Krs Lama, tidak perlu mengambil data ke DB menggunakan get() ataupun first(),
            // melainkan langsung isikan attribute nya ke instance nya
            $krsLama = new KrsLama([
                'mhs_nim'      => $apresiasiMhs->nim,
                'jkul_klkl_id' => $apresiasiDetil->klkl_id,
                'jkul_kelas'   => $dataKrs->jkul_kelas,
            ]);

            // NOTE: bila instance model tidak didapatkan dari DB, maka attribute "exists" akan bernilai false,
            // Sedangkan di method delete(), bila "exists" ini bernilai false, maka dia akan langsung return,
            // dan tidak akan menjalankan method performDeleteOnModel(), maka dari itu attribute ini perlu di set ke true
            $krsLama->exists = true;
            $krsLama->delete();

            // Hapus Apresiasi Detil
            $apresiasiDetil->delete();
        }

        // Hapus bukti kegiatan (klo ada)
        if ($apresiasiMhs->bukti_kegiatan) Storage::disk('bukti')->delete($apresiasiMhs->bukti_kegiatan);

        // Hapus Apresiasi Mhs
        $apresiasiMhs->delete();

        return back()->with('success', 'Nilai Apresiasi Mahasiswa berhasil dihapus.');
    }


    public function jsonGetNamaMhs($nim)
    {
        $nama = Mahasiswa::where('nim', $nim)->first('nama');

        return response()->json($nama);
    }

    public function jsonGetMatkulMhs($nim, $smt)
    {
        $matkul = KrsTf::query()
            ->where('mhs_nim', $nim)
            ->with(['kurikulum' => function ($kurikulum) {
                $kurikulum->addSelect('id', 'nama', 'sks');
            }])
            ->orderBy('jkul_klkl_id')
            ->orderBy('jkul_kelas')
            ->get([
                'jkul_klkl_id',
                'jkul_kelas',
                'mhs_nim',
                'sts_mk',
                'sts_pre',
                'pro_hdr',
                'n_uas',
            ]);

        // Ambil semua klkl_id di ApresiasiDetil yang memiliki data di ApresiasiMhs dengan smt dan nim yang dipass
        $apresiasiDetil = ApresiasiDetil::query()
            ->whereHas('mahasiswa', function ($mahasiswa) use ($nim, $smt) {
                $mahasiswa->where('smt', $smt)->where('nim', $nim);
            })
            ->get()
            ->pluck('klkl_id');

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

    // Ini cetak langsung ke browser / print client side
    public function cetak($id_apresiasi)
    {
        $apresiasiDetil = ApresiasiDetil::where('id_apresiasi', $id_apresiasi)->get();

        $apresiasiMhs = ApresiasiMhs::query()
            ->with([
                'krs' => function ($krs) use ($apresiasiDetil) {
                    $krs->whereIn('jkul_klkl_id', $apresiasiDetil->pluck('klkl_id'))
                        ->with('kurikulum');
                },
                'mhs'
            ])
            ->where('id_apresiasi', $id_apresiasi)
            ->first();


        return view('nilai-apresiasi.print', compact('apresiasiMhs'));
    }

    // Ini cetak menggunakan MPDF / print server side
    public function cetak_new($id_apresiasi)
    {
        $apresiasiDetil = ApresiasiDetil::where('id_apresiasi', $id_apresiasi)->get();

        $apresiasiMhs = ApresiasiMhs::query()
            ->with([
                'krs' => function ($krs) use ($apresiasiDetil) {
                    $krs->whereIn('jkul_klkl_id', $apresiasiDetil->pluck('klkl_id'))
                        ->with('kurikulum');
                },
                'mhs'
            ])
            ->where('id_apresiasi', $id_apresiasi)
            ->first();

        $document = new Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A4',
            'orientation' => 'P',
        ]);

        $document->WriteHTML(view('nilai-apresiasi.print_new', compact('apresiasiMhs')));

        // Parameter pertama adalah nama file nya, jangan lupa dikasih ".pdf"
        // Parameter kedua adalah destinasinya, mau ditampilin di browser, atau langsung di download, dll
        // Source : https://mpdf.github.io/reference/mpdf-functions/output.html
        $document->Output("Nilai Apresiasi {$apresiasiMhs->nim} Semester {$apresiasiMhs->smt}.pdf", "I");
    }
}
