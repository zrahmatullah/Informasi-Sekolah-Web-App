<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Raport - {{ $siswa->nama_lengkap_siswa }}</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            font-size: 13px;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-surat h2, .kop-surat h4 {
            margin: 0;
        }

        .info-siswa {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        .ttd {
            margin-top: 50px;
            width: 100%;
        }

        .ttd td {
            border: none;
            text-align: center;
            padding-top: 50px;
        }

        .left {
            text-align: left;
        }

        .text-left {
            text-align: left;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .table-container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h2>SEKOLAH DASAR AL MUHAJIRIN</h2>
        <h4>Jl. Pendidikan No. 123, Kota Contoh, Indonesia</h4>
    </div>

    <h3 style="text-align: center;">RAPORT SISWA</h3>

    <div class="info-siswa">
        <p><strong>Nama:</strong> {{ $siswa->nama_lengkap_siswa }}</p>
        <p><strong>Tahun Pelajaran:</strong> {{ $tahun->tahun_pelajaran }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="text-left">Mata Pelajaran</th>
                    <th>UH</th>
                    <th>UTS</th>
                    <th>Ujian</th>
                    <th>Rata-rata</th>
                    <th>Grade</th>
                    <th>Status Akademis</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_rata2 = 0;
                    $jumlah_mapel = count($nilai);

                    function getStatusAkademis($grade) {
                        return match ($grade) {
                            'A', 'B' => 'Lulus',
                            'C', 'D' => 'Remedial',
                            'E'       => 'Tidak Lulus',
                            default   => '-',
                        };
                    }
                @endphp

                @foreach ($nilai as $n)
                    <tr>
                        <td class="text-left">{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                        <td>{{ $n->nilai_tugas }}</td>
                        <td>{{ $n->nilai_uts }}</td>
                        <td>{{ $n->nilai_ujian }}</td>
                        <td>{{ $n->rata_rata }}</td>
                        <td>{{ $n->grade }}</td>
                        <td>{{ getStatusAkademis($n->grade) }}</td>
                    </tr>
                    @php $total_rata2 += $n->rata_rata; @endphp
                @endforeach

                <tr class="total-row">
                    <td class="text-left" colspan="4">Rata-rata Keseluruhan</td>
                    <td colspan="3">
                        {{ $jumlah_mapel > 0 ? number_format($total_rata2 / $jumlah_mapel, 2) : '0.00' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="ttd">
        <tr>
            <td class="left" style="width: 50%;">
                Siswa<br><br><br><br>
                ({{ $siswa->nama_lengkap_siswa }})
            </td>
            <td class="left" style="width: 50%;">
                Kota Contoh, {{ date('d-m-Y') }}<br>
                Wali Kelas<br><br><br><br>
                (___________________)
            </td>
        </tr>
    </table>

</body>
</html>
