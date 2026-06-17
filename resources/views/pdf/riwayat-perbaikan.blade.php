<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Perbaikan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #222;
        }

        h2 {
            text-align: center;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 16px;
        }

        .info-cetak {
            margin-bottom: 12px;
            font-size: 10px;
            color: #333;
        }

        .info-cetak table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-cetak td {
            border: none;
            padding: 2px 0;
        }

        .info-label {
            width: 90px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1d4ed8;
            color: white;
            padding: 6px;
            text-align: left;
        }

        td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: right;
            color: #777;
        }
    </style>
</head>
<body>
    <h2>Riwayat Perbaikan Laboratorium</h2>

    <p class="subtitle">
        {{ $lab ? 'Lab: ' . $lab->nm_lab : 'Semua Lab' }}
        @if($dari || $sampai)
            | Periode: {{ $dari ?? '-' }} s/d {{ $sampai ?? '-' }}
        @endif
    </p>

    <div class="info-cetak">
        <table>
            <tr>
                <td class="info-label">Dicetak oleh</td>
                <td>: {{ $dicetakOleh ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal cetak</td>
                <td>: {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Laporan</th>
                <th>Lab</th>
                <th>Pelapor</th>
                <th>Kategori</th>
                <th>Tgl Ubah</th>
                <th>Status</th>
                <th>Diubah Oleh</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayats as $index => $riwayat)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $riwayat->perbaikan?->id_laporan ?? '-' }}</td>
                    <td>{{ $riwayat->perbaikan?->laporan?->lab?->nm_lab ?? '-' }}</td>
                    <td>{{ $riwayat->perbaikan?->laporan?->nm_pelapor ?? '-' }}</td>
                    <td>{{ $riwayat->perbaikan?->laporan?->kategori ?? '-' }}</td>
                    <td>{{ $riwayat->tgl_ubah?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $riwayat->perbaikan?->status_perbaikan ?? '-')) }}</td>
                    <td>{{ $riwayat->user?->nm_user ?? '-' }}</td>
                    <td>{{ $riwayat->catatan_rw ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        Dicetak oleh {{ $dicetakOleh ?? '-' }} pada {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>