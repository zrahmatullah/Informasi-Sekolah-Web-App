<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nilai Raport</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }

        th:nth-child(2), td:nth-child(2) {
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .highlight {
            background-color: #ffffcc;
            font-weight: bold;
        }

        .no-border td {
            border: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h4>NILAI AKHIR GANJIL KELAS {{ $kelas->nama_kelas ?? '-' }}</h4>
        <p style="margin-top: -10px;">SD ISLAM PLUS AL MUHAJIRIN<br>Tahun Pelajaran {{ $tahun->tahun_pelajaran ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 120px;">Nama</th>
                @foreach ($mapels as $mapel)
                    <th style="width: 40px;">{{ $mapel->nama_mapel }}</th>
                @endforeach
                <th style="width: 50px;background-color: purple; color: white;">Jumlah</th>
                <th style="width: 45px;background-color: maroon; color: white;">Rata2</th>
                <th style="width: 35px;background-color: rgb(66, 66, 201); color: white;">Rank</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa['nama_lengkap_siswa'] }}</td>
                    @foreach ($mapels as $mapel)
                        <td>
                            {{ is_numeric($siswa['nilai'][$mapel->id] ?? null) ? $siswa['nilai'][$mapel->id] : '-' }}
                        </td>
                    @endforeach
                    <td style="background-color: purple;color: white;">{{ $siswa['jumlah'] ?? 0 }}</td>
                    <td style="background-color: maroon; color: white;">{{ number_format($siswa['rata_rata'] ?? 0, 2) }}</td>
                    <td style="background-color: rgb(66, 66, 201); color: white;">{{ $siswa['rank'] }}</td>
                </tr>
            @endforeach

            <tr class="highlight">
                <td colspan="2">Rata-Rata</td>
                @foreach ($mapels as $mapel)
                    <td>
                        {{
                            ceil(
                                collect($data)->pluck('nilai')->map(function ($nilai) use ($mapel) {
                                    return is_numeric($nilai[$mapel->id] ?? null) ? $nilai[$mapel->id] : null;
                                })->filter()->avg() ?? 0
                            )
                        }}
                    </td>
                @endforeach
                <td>{{ ceil(collect($data)->pluck('jumlah')->filter()->avg() ?? 0) }}</td>
                <td>{{ ceil(collect($data)->pluck('rata_rata')->filter()->avg() ?? 0) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="no-border" style="margin-top: 25px;">
        <tr>
            <td style="text-align: left;">
                Kepala Sekolah<br><br><br><br>
                <strong>Admministrator System, S.Pd.</strong>
            </td>
            <td style="text-align: right;">
                Tangerang Selatan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Wali Kelas<br><br><br><br>
                <strong>{{ $wali_kelas ?? '...' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
