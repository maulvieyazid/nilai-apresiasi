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
                            <!-- Jika canSubmit bernilai true maka lakukan submit, jika false, maka cegah submit -->
                            <form class="form" method="POST" action="{{ route('nilaiapresiasi.store') }}" @submit.prevent="canSubmit && $el.submit()">
                                @csrf
                                <div class="row">
                                    <!-- Semester -->
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="smt">Semester</label>
                                            <input type="text" id="smt" class="form-control" placeholder="Semester" name="smt" required>
                                        </div>
                                    </div>
                                    <!-- NIM -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="nim">NIM</label>
                                            <input type="text" id="nim" class="form-control" placeholder="NIM" name="nim" x-mask="99999999999" required x-ref="nim" @change="getNamaMhs()">
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
                                            <label for="jenis_kegiatan">Jenis Kegiatan</label>
                                            <input type="text" id="jenis_kegiatan" class="form-control" name="jenis_kegiatan" placeholder="contoh: Lomba Fotografi" required>
                                        </div>
                                    </div>
                                    <!-- Prestasi Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="prestasi_kegiatan">Prestasi Kegiatan</label>
                                            <input type="text" id="prestasi_kegiatan" class="form-control" name="prestasi_kegiatan" placeholder="contoh: Juara 1" required>
                                        </div>
                                    </div>
                                    <!-- Tingkat Kegiatan -->
                                    <div class="col-4">
                                        <div class="form-group">
                                            <label for="tingkat_kegiatan">Tingkat Kegiatan</label>
                                            <input type="text" id="tingkat_kegiatan" class="form-control" name="tingkat_kegiatan" placeholder="contoh: Nasional / Regional / Internasional" required>
                                        </div>
                                    </div>
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
                                            <input class="form-control" type="file" id="bukti_kegiatan" name="bukti_kegiatan">
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
                                                    <th class="text-center">Kode</th>
                                                    <th class="text-center">Nama</th>
                                                    <th class="text-center">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="matkul in semuaMatkul">
                                                    <tr :class="!matkul.centang ? 'table-secondary' : ''" x-id="['nilai-matkul']">
                                                        <td class="text-center">
                                                            <!-- Jika dicentang, maka langsung focus ke inputan nilai -->
                                                            <input type="checkbox" id="checkbox1" class="form-check-input" x-model="matkul.centang" @change="if ($el.checked) document.getElementById($id('nilai-matkul')).focus()">
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.kode"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span x-text="matkul.nama"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="number" step="any" class="form-control" :id="$id('nilai-matkul')" placeholder="Nilai" :value="matkul.nilai" x-bind:disabled="!matkul.centang"
                                                                :name="`nilai_matkul[${matkul.kode}]`">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn icon icon-left btn-primary me-1 mb-1 text-white" :class="canSubmit ? '' : 'disabled'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                                <path
                                                    d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z" />
                                            </svg>
                                            Simpan
                                        </button>
                                    </div>
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

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('create_nilai_apresiasi', () => {
                return {
                    canSubmit: true,
                    semuaMatkul: [{
                            centang: false,
                            kode: '123456',
                            nama: 'Pemrograman Web',
                            nilai: '',
                        },
                        {
                            centang: false,
                            kode: '456789',
                            nama: 'Pemrograman Basis Data',
                            nilai: '',
                        },
                        {
                            centang: false,
                            kode: '101112',
                            nama: 'Pemrograman Komputer',
                            nilai: '',
                        },
                    ],

                    getNamaMhs() {
                        this.canSubmit = true;
                        this.$refs.nama.classList.remove('is-invalid');

                        const nim = this.$refs.nim.value;

                        let url = "{{ route('nilaiapresiasi.json.get.nama_mhs', ['nim' => ':nim']) }}";
                        url = url.replace(':nim', nim);

                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                this.$refs.nama.value = data.nama || 'Mahasiswa tidak ditemukan';

                                // Jika mahasiswa tidak ditemukan
                                if (!data.nama) {
                                    this.$refs.nama.classList.add('is-invalid');

                                    this.canSubmit = false
                                }
                            });
                    },
                };
            });
        });
    </script>
@endpush
