<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <title>Nilai Apresiasi {{ $apresiasiMhs->nim }} Semester {{ $apresiasiMhs->smt }}</title>
</head>

<body>
    <!-- Header Judul -->
    <div class="row">
        <div class="col text-center">
            <p>KONVERSI NILAI ANGKA -> HURUF</p>
            <h3 style="color: rgb(39,112,192)">APRESIASI - {{ $apresiasiMhs->smt }}</h3>
        </div>
    </div>

    <!-- NIM dan Nama -->
    <div class="row mt-5">
        <div class="col-5">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td>{{ $apresiasiMhs->smt }}</td>
                    </tr>
                    <tr>
                        <td>NAMA</td>
                        <td>:</td>
                        <td>{{ $apresiasiMhs->mhs->nama ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Detil Matkul Apresiasi -->
    <div class="row">
        <div class="col">
            <table class="table table-bordered border-0">
                <thead>
                    <tr>
                        <td class="text-center fw-bold">NO.</td>
                        <td class="text-center fw-bold">KODE MK</td>
                        <td class="text-center fw-bold">NAMA MK</td>
                        <td class="text-center fw-bold">KELAS</td>
                        <td class="text-center fw-bold">NILAI ANGKA</td>
                        <td class="text-center fw-bold">NILAI HURUF</td>
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
    <div class="row mt-4">
        <div class="col d-flex justify-content-end">
            <div class="text-center me-5">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(e) {
            window.print();
        });
    </script>
</body>

</html>
