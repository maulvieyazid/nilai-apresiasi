@extends('layouts.app')

@section('content')
    <div class="page-heading">
        <h3>Entry Nilai Apresiasi Mahasiswa</h3>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="text-end">
                            <a href="{{ route('nilaiapresiasi.create') }}" class="btn icon icon-left btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                </svg>
                                Tambah
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">Semester</th>
                                        <th rowspan="2" class="text-center">NIM</th>
                                        <th rowspan="2" class="text-center">Nama</th>
                                        <th colspan="4" class="text-center">Kegiatan</th>
                                        <th rowspan="2" class="text-center" style="width: 13%">
                                            Aksi
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">
                                            <!-- style="width: 25%" -->
                                            Jenis
                                        </th>
                                        <th class="text-center">
                                            <!-- style="width: 6%" -->
                                            Prestasi
                                        </th>
                                        <th class="text-center">
                                            <!-- style="width: 15%" -->
                                            Tingkat
                                        </th>
                                        <th class="text-center" style="width: 16%">
                                            Bukti
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($semuaApresiasiMhs as $apresiasiMhs)
                                        <tr>
                                            <td class="text-center">
                                                {{ $apresiasiMhs->smt }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apresiasiMhs->nim }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apresiasiMhs->mhs->nama ?? '' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apresiasiMhs->jenis_kegiatan }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apresiasiMhs->prestasi_kegiatan }}
                                            </td>
                                            <td class="text-center">
                                                {{ $apresiasiMhs->tingkat_kegiatan }}
                                            </td>
                                            <td class="text-center">
                                                @if ($apresiasiMhs->bukti_kegiatan)
                                                    <a href="{{ config('filesystems.disks.bukti.url') . '/' . $apresiasiMhs->bukti_kegiatan }}" title="{{ $apresiasiMhs->bukti_kegiatan }}" target="_blank">
                                                        {{ Str::limit($apresiasiMhs->bukti_kegiatan, 15) }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div>
                                                    <!-- Tombol Edit -->
                                                    <a href="{{ route('nilaiapresiasi.edit', $apresiasiMhs->id_apresiasi) }}" class="btn icon btn-sm btn-warning text-dark" title="Edit Nilai Apresiasi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                            <path
                                                                d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z" />
                                                        </svg>
                                                    </a>
                                                    <!-- Tombol Cetak -->
                                                    <a href="{{ route('nilaiapresiasi.cetak_new', $apresiasiMhs->id_apresiasi) }}" class="btn icon btn-sm btn-success" title="Cetak Nilai Apresiasi" target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                                            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
                                                            <path
                                                                d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
                                                        </svg>
                                                    </a>
                                                    <!-- Tombol Hapus -->
                                                    <button type="button" class="btn icon btn-sm btn-danger" onclick="hapus(this, {{ $apresiasiMhs->toJson() }})" title="Hapus Nilai Apresiasi">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                            <path
                                                                d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z" />
                                                        </svg>
                                                        <form action="{{ route('nilaiapresiasi.destroy', $apresiasiMhs->id_apresiasi) }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        async function hapus(btn, apresiasiMhs) {
            const {
                value
            } = await Swal.fire({
                title: `<h3>Yakin ingin menghapus Nilai Apresiasi ini?</h3>`,
                html: `<table class='table'>
                            <tr>
                                <td>Semester</td>
                                <td>NIM</td>
                                <td>Jenis</td>
                            </tr>
                            <tr>
                                <td>${apresiasiMhs.smt}</td>
                                <td>${apresiasiMhs.nim}</td>
                                <td>${apresiasiMhs.jenis_kegiatan}</td>
                            </tr>

                       </table>`,
                icon: 'warning',
                confirmButtonText: 'Iya',
                showCancelButton: true,
                cancelButtonText: 'Batal',
            })

            if (value) {
                btn.querySelector('form').submit();
            }
        }
    </script>
@endpush
