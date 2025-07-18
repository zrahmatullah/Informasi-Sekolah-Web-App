<!DOCTYPE html>
<html>
<head>
    <title>Raport Semua Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        table { border-collapse: collapse; margin-top: 10px; width: 100%; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        .kop { text-align: center; line-height: 1.5; margin-bottom: 10px; }
        .page-break { page-break-after: always; }
        .ttd { margin-top: 40px; text-align: right; padding-right: 50px; }
        hr { border: 1px solid black; margin-top: 4px; }
    </style>
</head>
<body>

@php
    if (!function_exists('getStatusAkademis')) {
        function getStatusAkademis($grade) {
            switch ($grade) {
                case 'A':
                case 'B':
                    return 'Lulus';
                case 'C':
                case 'D':
                    return 'Remedial';
                case 'E':
                    return 'Tidak Lulus';
                default:
                    return '-';
            }
        }
    }
@endphp

@foreach($dataRaport as $data)
    <div class="kop">
        <h2>SD NEGERI CONTOH</h2>
        <p>Jl. Pendidikan No. 123, Jakarta<br>
        Telp: (021) 12345678</p>
        <hr>
    </div>

    <h3>Raport Tahun Pelajaran: {{ $tahun->tahun_pelajaran ?? '-' }}</h3>
    <h4>Nama Siswa: {{ $data['siswa']->nama_lengkap_siswa ?? '-' }}</h4>
    <p>Kelas: {{ $kelas->nama_kelas ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai Tugas</th>
                <th>Nilai UTS</th>
                <th>Nilai Ujian</th>
                <th>Rata-rata</th>
                <th>Grade</th>
                <th>Status Akademis</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalRata = 0;
                $jumlahMapel = count($data['nilai']);
            @endphp

            @if($data['nilai']->isEmpty())
                <tr>
                    <td colspan="8"><em>Belum ada nilai yang diinput.</em></td>
                </tr>
            @else
                @foreach($data['nilai'] as $index => $nilai)
                    @php
                        $totalRata += $nilai->rata_rata;
                        $statusAkademis = getStatusAkademis($nilai->grade);
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ optional($nilai->mataPelajaran)->nama_mapel ?? '-' }}</td>
                        <td>{{ $nilai->nilai_tugas ?? '-' }}</td>
                        <td>{{ $nilai->nilai_uts ?? '-' }}</td>
                        <td>{{ $nilai->nilai_ujian ?? '-' }}</td>
                        <td>{{ number_format($nilai->rata_rata ?? 0, 2) }}</td>
                        <td>{{ $nilai->grade ?? '-' }}</td>
                        <td>{{ $statusAkademis }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>

        @if(!$data['nilai']->isEmpty())
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align: right;">Rata-rata Keseluruhan</th>
                    <th>{{ $jumlahMapel > 0 ? number_format($totalRata / $jumlahMapel, 2) : '0.00' }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="ttd">
        <p>Guru Kelas</p>
        <br><br><br>
        <p>_________________________</p>
    </div>

    <div class="page-break"></div>
@endforeach

</body>
</html>
