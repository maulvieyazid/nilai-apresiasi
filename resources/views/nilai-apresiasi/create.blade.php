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
    <div x-data="create_nilai_apresiasi">
        <div class="page-heading">
            <h3>Tambah Nilai Apresiasi Mahasiswa</h3>
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
                            <div class="mb-3">
                                <span class="text-danger">*</span> wajib diisi
                            </div>
                            <!-- Jika canSubmit bernilai true maka lakukan submit, jika false, maka cegah submit -->
                            <form class="form" enctype="multipart/form-data" id="formTambahNilaiApresiasi" method="POST" action="{{ route('nilaiapresiasi.store') }}" @submit.prevent="canSubmit && $el.submit()">
                                @csrf
                                <div class="row">
                                    <!-- Semester -->
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="smt">
                                                Semester
                                            </label>
                                            <input type="text" id="smt" class="form-control" placeholder="Semester" name="smt" readonly value="{{ $smt->smt_aktif }}" title="Semester Aktif" x-ref="smt" {{-- @input.debounce="getMatkulMhs()" --}}>
                                        </div>
                                    </div>
                                    <!-- NIM -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="nim">
                                                NIM <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="nim" class="form-control" placeholder="NIM" name="nim" x-mask="99999999999" required x-ref="nim" @input.debounce="getNamaMhs()">
                                        </div>
                                    </div>
                                    <!-- Nama -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="nama">Nama</label>
                                            <input type="text" id="nama" class="form-control" placeholder="Nama" readonly x-ref="nama">
                                        </div>
                                    </div>
                                    <!-- Jenis Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="jenis_kegiatan">
                                                Jenis Kegiatan <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="jenis_kegiatan" class="form-control" name="jenis_kegiatan" placeholder="contoh: Lomba Fotografi" required>
                                        </div>
                                    </div>
                                    <!-- Prestasi Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="prestasi_kegiatan">
                                                Prestasi Kegiatan <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="prestasi_kegiatan" class="form-control" name="prestasi_kegiatan" placeholder="contoh: Juara 1" required>
                                        </div>
                                    </div>
                                    <!-- Tingkat Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="tingkat_kegiatan">
                                                Tingkat Kegiatan <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="tingkat_kegiatan" class="form-control" name="tingkat_kegiatan" placeholder="contoh: Nasional / Regional / Internasional" required>
                                        </div>
                                    </div>
                                    <!-- Keterangan -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan (Opsional)</label>
                                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control" placeholder="Keterangan (Opsional)"></textarea>
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
                                        </div>
                                    </div>
                                    <!-- Tabel Pemilihan Matakuliah yang akan dikonversi -->
                                    <div class="col-12 mt-4">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" rowspan="2">Pilih</th>
                                                    <th class="text-center" colspan="100%">Matakuliah yang dikonversi</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Kelas</th>
                                                    <th class="text-center">Kode</th>
                                                    <th class="text-center">Nama</th>
                                                    <th class="text-center">SKS</th>
                                                    <th class="text-center">Nilai</th>
                                                    <th class="text-center">Nilai Huruf</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="matkul in semuaMatkul">
                                                    <tr :class="!matkul.centang ? 'table-secondary' : ''" x-id="['matkul-konversi']">

                                                        <!-- Input type hidden ini untuk menyertakan data tambahan di array nilai_matkul -->
                                                        <!-- x-bind disabled digunakan untuk mendisable input hidden agar tidak ikut tersubmit, saat barisnya tidak dicentang -->
                                                        <input type="hidden" :name="`nilai_matkul[${matkul.klkl_id}][klkl_id]`" :value="matkul.klkl_id" x-bind:disabled="!matkul.centang">
                                                        <input type="hidden" :name="`nilai_matkul[${matkul.klkl_id}][nilai_huruf]`" :value="matkul.nilai_huruf" x-bind:disabled="!matkul.centang">
                                                        <input type="hidden" :name="`nilai_matkul[${matkul.klkl_id}][sts_mk]`" :value="matkul.sts_mk" x-bind:disabled="!matkul.centang">
                                                        <input type="hidden" :name="`nilai_matkul[${matkul.klkl_id}][sks]`" :value="matkul.kurikulum.sks" x-bind:disabled="!matkul.centang">

                                                        <td class="text-center">
                                                            <!-- Jika dicentang, maka langsung focus ke inputan nilai -->
                                                            <input type="checkbox" id="checkbox1" class="form-check-input" x-model="matkul.centang" @change="if ($el.checked) document.getElementById($id('matkul-konversi')).focus()">
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.jkul_kelas"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.klkl_id"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.kurikulum.nama"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.kurikulum.sks"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="number" step="any" class="form-control" :id="$id('matkul-konversi')" placeholder="Nilai" x-model="matkul.nilai_angka" x-bind:disabled="!matkul.centang"
                                                                :name="`nilai_matkul[${matkul.klkl_id}][nilai_angka]`" @input.debounce="getNilaiHuruf(matkul)">
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.nilai_huruf"></span>

                                                            <div class="spinner-border text-dark d-none spinner-border-sm" role="status" :id="$id('matkul-konversi', 'loader')">
                                                                <span class="visually-hidden">Loading...</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>

                                                <!-- Kalo semuaMatkul kosong -->
                                                <template x-if="!semuaMatkul.length">
                                                    <tr>
                                                        <td colspan="100%" class="text-center">
                                                            <span class="fw-bold" x-text="matkulNotExistText"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <!-- submit.outside digunakan untuk mendisable button saat submit, sehingga tidak akan terjadi submit dua kali -->
                                        <button type="button" @click="simpan()" class="btn icon icon-left btn-primary me-1 mb-1 text-white" :class="canSubmit ? '' : 'disabled'" {{-- @submit.outside="$el.classList.add('disabled')" --}}>
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
    <script defer src="{{ asset('assets/vendors/alpine/alpine-mask@3.10.5.min.js') }}"></script>
    <script defer src="{{ asset('assets/vendors/alpine/alpine@3.10.5.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('create_nilai_apresiasi', () => {
                return {
                    matkulNotExistText: '',
                    canSubmit: true,
                    semuaMatkul: [],

                    getNamaMhs() {
                        this.canSubmit = true;
                        // Hilangkan validasi error pada inputan nama
                        this.$refs.nama.classList.remove('is-invalid');

                        const nim = this.$refs.nim.value;

                        let url = "{{ route('nilaiapresiasi.json.get.nama_mhs', ['nim' => ':nim']) }}";
                        url = url.replace(':nim', nim);

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                this.$refs.nama.value = data.nama || 'Mahasiswa tidak ditemukan';
                                this.semuaMatkul = [];

                                // Jika mahasiswa tidak ditemukan
                                if (!data.nama) {
                                    this.matkulNotExistText = '';
                                    // Tampilkan validasi error pada inputan nama
                                    this.$refs.nama.classList.add('is-invalid');
                                    this.canSubmit = false
                                    return;
                                }

                                this.getMatkulMhs();
                            });
                    },

                    getMatkulMhs() {
                        this.matkulNotExistText = '';
                        this.semuaMatkul = [];

                        // Klo semester belum diisi
                        /* if (!this.$refs.smt.value) {
                            this.matkulNotExistText = 'Mohon isi semester';
                            return;
                        } */

                        // Klo nim belum diisi
                        if (!this.$refs.nim.value) {
                            this.matkulNotExistText = 'Mohon isi NIM';
                            return;
                        }

                        const smt = this.$refs.smt.value;
                        const nim = this.$refs.nim.value;

                        let url = "{{ route('nilaiapresiasi.json.get.matkul_mhs', ['nim' => ':nim', 'smt' => ':smt']) }}";
                        url = url.replace(':nim', nim).replace(':smt', smt);

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                // Klo data matkul nya null, maka return
                                if (!data.matkul) {
                                    this.matkulNotExistText = `Data Matakuliah Semester ${smt} dengan NIM ${nim} tidak ada`;
                                    return;
                                }

                                // Menambahkan default attribut centang, nilai_angka, nilai_huruf
                                const matkul = data.matkul.map(matkul => {
                                    matkul.centang = false;
                                    matkul.nilai_angka = null;
                                    matkul.nilai_huruf = '';
                                    return matkul;
                                });

                                // Set semuaMatkul
                                this.semuaMatkul = matkul;
                            });
                    },

                    getNilaiHuruf(matkul) {
                        const loader_id = this.$id('matkul-konversi', 'loader')
                        const loader = document.getElementById(loader_id);

                        // Tampilkan loader / spinner
                        loader.classList.remove('d-none');
                        matkul.nilai_huruf = '';

                        // Jika nilai angkanya kosong
                        if (!matkul.nilai_angka) {
                            // Sembunyikan loader / spinner
                            loader.classList.add('d-none');
                            return;
                        }

                        let url = "{{ route('nilaiapresiasi.json.get.nilai_huruf', ['nilai_angka' => ':nilai_angka']) }}";
                        url = url.replace(':nilai_angka', matkul.nilai_angka);

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                // Cast nilai_angka ke Number untuk menghilangkan angka 0 di depan
                                matkul.nilai_angka = Number(matkul.nilai_angka);

                                // Set Nilai Huruf
                                matkul.nilai_huruf = data.nilai_huruf || 'Nilai tidak valid';
                                this.canSubmit = true;

                                // Klo nilai huruf nya null, maka jangan boleh submit
                                if (!data.nilai_huruf) this.canSubmit = false;
                            })
                            .finally(() => {
                                // Sembunyikan loader / spinner
                                loader.classList.add('d-none');
                            });
                    },

                    async simpan() {
                        // Cek sekaligus trigger validasi form html
                        if (!document.querySelector('#formTambahNilaiApresiasi').reportValidity()) {
                            return;
                        }

                        // Disable btn simpan
                        this.$el.classList.add('disabled');

                        const {
                            value
                        } = await Swal.fire({
                            title: `<h3>Yakin ingin menyimpan Nilai Apresiasi?</h3>`,
                            icon: 'warning',
                            confirmButtonText: 'Iya',
                            showCancelButton: true,
                            cancelButtonText: 'Batal',
                        })

                        if (value) {
                            document.querySelector('#formTambahNilaiApresiasi').submit();
                            return;
                        }

                        // Enable btn simpan
                        this.$el.classList.remove('disabled');
                    },
                };
            });
        });
    </script>
@endpush
