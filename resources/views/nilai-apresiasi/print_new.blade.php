<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <title>Nilai Apresiasi {{ $apresiasiMhs->nim }} Semester {{ $apresiasiMhs->smt }}</title>
    <style>
        table.table-bordered,
        table.table-bordered thead tr,
        table.table-bordered thead tr td,
        table.table-bordered tbody tr,
        table.table-bordered tbody tr td,
            {
            border: 1px solid;
        }
    </style>
</head>

<body style="/* font-family: Calibri, sans-serif */">
    <!-- Header Judul -->
    <div class="row">
        <div class="col text-center">
            <p>KONVERSI NILAI ANGKA
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                </svg>
                HURUF
            </p>
            <h3 style="color: rgb(39,112,192); font-weight: bold; font-size: 20px">
                APRESIASI - {{ $apresiasiMhs->smt }}
            </h3>
        </div>
    </div>

    <!-- NIM dan Nama -->
    <div class="row" style="margin-top: 1.5rem;">
        <div class="col">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td style="width: 15%">NIM</td>
                        <td style="width: 5%">:</td>
                        <td>{{ $apresiasiMhs->nim }}</td>
                    </tr>
                    <tr>
                        <td style="width: 15%">NAMA</td>
                        <td style="width: 5%">:</td>
                        <td>{{ $apresiasiMhs->mhs->nama ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Detil Matkul Apresiasi -->
    <div class="row">
        <div class="col">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td class="text-center">
                            NO.
                        </td>
                        <td class="text-center">
                            KODE MK
                        </td>
                        <td class="text-center">
                            NAMA MK
                        </td>
                        <td class="text-center">
                            KELAS
                        </td>
                        <td class="text-center" style="width: 15%">
                            NILAI ANGKA
                        </td>
                        <td class="text-center" style="width: 15%">
                            NILAI HURUF
                        </td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($apresiasiMhs->krs as $krs)
                        <tr>
                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td class="text-center">
                                {{ $krs->jkul_klkl_id }}
                            </td>
                            <td class="text-center">
                                {{ $krs->kurikulum->nama ?? '' }}
                            </td>
                            <td class="text-center">
                                {{ $krs->jkul_kelas }}
                            </td>
                            <td class="text-center">
                                {{ $krs->n_uas }}
                            </td>
                            <td class="text-center">
                                <!-- Ini Accessor -->
                                {{ $krs->nilai_huruf }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TTD -->
    <div style="clear: both; margin-top: 1.5rem;"></div>
    <div style="width: 30%; float: right;">
        <div class="text-center me-5" style="page-break-inside: avoid">
            <p>Surabaya, {{ now()->format('d-m-Y') }}</p>
            <br>
            <br>
            <br>
            <p>
                <u>M.M. Sekar Dewanti</u>
                <br>
                KABAG AAK
            </p>
        </div>
    </div>
    <div style="clear: both"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            window.print();
        });
    </script>
</body>

</html>
