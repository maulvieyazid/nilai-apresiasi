@extends('layouts.app')

@push('styles')
    <style>
        /* Untuk menghapus/menyembunyikan panah di input type number */
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Untuk menghapus/menyembunyikan panah di input type number */
    </style>
@endpush

@section('content')
    <div x-data="edit_nilai_apresiasi">
        <div class="page-heading">
            <h3>Edit Nilai Apresiasi Mahasiswa</h3>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <a href="{{ route('nilaiapresiasi.index') }}" class="btn icon icon-left btn-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z" />
                                    </svg>
                                    Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Jika canSubmit bernilai true maka lakukan submit, jika false, maka cegah submit -->
                            <form class="form" enctype="multipart/form-data" method="POST" action="{{ route('nilaiapresiasi.update', $apresiasiMhs->id_apresiasi) }}" @submit.prevent="canSubmit && $el.submit()">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Semester -->
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="smt">Semester</label>
                                            <input type="text" id="smt" class="form-control" placeholder="Semester" value="{{ $apresiasiMhs->smt }}" disabled>
                                        </div>
                                    </div>
                                    <!-- NIM -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="nim">NIM</label>
                                            <input type="text" id="nim" class="form-control" placeholder="NIM" value="{{ $apresiasiMhs->nim }}" disabled>
                                        </div>
                                    </div>
                                    <!-- Nama -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="nama">Nama</label>
                                            <input type="text" id="nama" class="form-control" placeholder="Nama" value="{{ $apresiasiMhs->mhs->nama }}" disabled>
                                        </div>
                                    </div>
                                    <!-- Jenis Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="jenis_kegiatan">Jenis Kegiatan</label>
                                            <input type="text" id="jenis_kegiatan" class="form-control" name="jenis_kegiatan" placeholder="contoh: Lomba Fotografi" value="{{ $apresiasiMhs->jenis_kegiatan }}" required>
                                        </div>
                                    </div>
                                    <!-- Prestasi Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="prestasi_kegiatan">Prestasi Kegiatan</label>
                                            <input type="text" id="prestasi_kegiatan" class="form-control" name="prestasi_kegiatan" placeholder="contoh: Juara 1" value="{{ $apresiasiMhs->prestasi_kegiatan }}" required>
                                        </div>
                                    </div>
                                    <!-- Tingkat Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="tingkat_kegiatan">Tingkat Kegiatan</label>
                                            <input type="text" id="tingkat_kegiatan" class="form-control" name="tingkat_kegiatan" placeholder="contoh: Nasional / Regional / Internasional" value="{{ $apresiasiMhs->tingkat_kegiatan }}" required>
                                        </div>
                                    </div>
                                    <!-- Keterangan -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan (Opsional)</label>
                                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control" placeholder="Keterangan (Opsional)">{{ $apresiasiMhs->keterangan }}</textarea>
                                        </div>
                                    </div>
                                    <!-- Bukti Kegiatan -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="bukti_kegiatan" class="form-label">Bukti Kegiatan</label>
                                            <input class="form-control" type="file" id="bukti_kegiatan" name="bukti_kegiatan" accept="application/pdf, image/jpeg, image/png, .docx"
                                                onchange="(this.files[0] && this.files[0].size > 12582912) ? this.classList.add('is-invalid') : this.classList.remove('is-invalid');">
                                            <div class="invalid-feedback">
                                                Bukti Kegiatan tidak boleh lebih dari 10 MB
                                            </div>
                                            @if ($apresiasiMhs->bukti_kegiatan)
                                                <a href="{{ config('filesystems.disks.bukti.url') . '/' . $apresiasiMhs->bukti_kegiatan }}" target="_blank">
                                                    {{ $apresiasiMhs->bukti_kegiatan }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Tabel Pemilihan Matakuliah yang akan dikonversi -->
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    {{-- <th class="text-center" rowspan="2">Pilih</th> --}}
                                                    <th class="text-center" colspan="100%">Matakuliah yang dikonversi</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Kode</th>
                                                    <th class="text-center">Nama</th>
                                                    <th class="text-center">SKS</th>
                                                    <th class="text-center">Nilai</th>
                                                    <th class="text-center">Nilai Huruf</th>
                                                </tr>
                                            </thead>
                                            <tbody x-init="semuaKrs = {{ $semuaKrs->toJson() }}">
                                                <template x-for="krs in semuaKrs">
                                                    <tr x-id="['matkul-konversi']">

                                                        <!-- Input type hidden ini untuk menyertakan data tambahan di array nilai_matkul -->
                                                        <input type="hidden" :name="`nilai_matkul[${krs.jkul_klkl_id}][klkl_id]`" :value="krs.jkul_klkl_id">
                                                        <input type="hidden" :name="`nilai_matkul[${krs.jkul_klkl_id}][nilai_huruf]`" :value="krs.n_huruf">
                                                        <input type="hidden" :name="`nilai_matkul[${krs.jkul_klkl_id}][sts_mk]`" :value="krs.sts_mk">

                                                        <td class="text-center">
                                                            <span x-text="krs.jkul_klkl_id"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="krs.kurikulum.nama"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="krs.kurikulum.sks"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="number" step="any" class="form-control" :id="$id('matkul-konversi')" placeholder="Nilai" x-model="krs.n_akhir" :name="`nilai_matkul[${krs.jkul_klkl_id}][nilai_angka]`"
                                                                @input.debounce="getNilaiHuruf(krs)">
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="krs.n_huruf"></span>

                                                            <div class="spinner-border text-dark d-none spinner-border-sm" role="status" :id="$id('matkul-konversi', 'loader')">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <!-- submit.outside digunakan untuk mendisable button saat submit, sehingga tidak akan terjadi submit dua kali -->
                                        <button type="submit" class="btn icon icon-left btn-primary me-1 mb-1 text-white" :class="canSubmit ? '' : 'disabled'" @submit.outside="$el.classList.add('disabled')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                                <path
                                                    d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z" />
                                            </svg>
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script defer src="{{ asset('assets/vendors/alpine/alpine-mask@3.10.5.min.js') }}"></script> --}}
    <script defer src="{{ asset('assets/vendors/alpine/alpine@3.10.5.min.js') }}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('edit_nilai_apresiasi', () => {
                return {
                    semuaKrs: [],
                    canSubmit: true,

                    getNilaiHuruf(krs) {
                        const loader_id = this.$id('matkul-konversi', 'loader')
                        const loader = document.getElementById(loader_id);

                        // Tampilkan loader / spinner
                        loader.classList.remove('d-none');
                        krs.n_huruf = '';

                        // Jika nilai angkanya kosong
                        if (!krs.n_akhir) {
                            // Sembunyikan loader / spinner
                            loader.classList.add('d-none');
                            return;
                        }

                        let url = "{{ route('nilaiapresiasi.json.get.nilai_huruf', ['nilai_angka' => ':nilai_angka']) }}";
                        url = url.replace(':nilai_angka', krs.n_akhir);

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                // Cast nilai_angka ke Number untuk menghilangkan angka 0 di depan
                                krs.n_akhir = Number(krs.n_akhir);

                                // Set Nilai Huruf
                                krs.n_huruf = data.nilai_huruf || 'Nilai tidak valid';
                                this.canSubmit = true;

                                // Klo nilai huruf nya null, maka jangan boleh submit
                                if (!data.nilai_huruf) this.canSubmit = false;
                            })
                            .finally(() => {
                                // Sembunyikan loader / spinner
                                loader.classList.add('d-none');
                            });
                    },
                };
            });
        });
    </script>
@endpush
