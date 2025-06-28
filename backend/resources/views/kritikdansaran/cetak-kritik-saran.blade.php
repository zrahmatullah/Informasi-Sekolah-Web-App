<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $judul }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .header p {
            margin: 0;
            font-size: 12px;
        }

        .info {
            margin-bottom: 10px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        {{-- Logo sekolah jika ada --}}
        {{-- <img src="{{ public_path('logo.png') }}" alt="Logo"> --}}
        <h1>SD Negeri Contoh</h1>
        <p>Jl. Pendidikan No. 1, Kota Edukasi</p>
    </div>

    <div class="info">
        <strong>Laporan:</strong> {{ $judul }} <br>
        <strong>Dicetak oleh:</strong> {{ $username ?? 'Tidak diketahui' }} <br>
        <strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Judul</th>
                <th>Isi</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Guru Tujuan</th>
                <th>Tanggapan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kritikSarans as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $item->judul ?? '-' }}</td>
                    <td>{{ $item->isi ?? '-' }}</td>
                    <td>{{ $item->siswa->nama_lengkap_siswa ?? '-' }}</td>
                    <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $item->guru->nama ?? '-' }}</td>
                    <td>{{ $item->tanggapan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem pada {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>

</body>
</html>
