<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Rekonsiliasi — {{ $periode?->nama }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #0b1c30; margin: 0; padding: 20px; }
        h1 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        h2 { font-size: 13px; font-weight: 600; color: #40484f; margin-bottom: 20px; }
        .header { text-align: center; border-bottom: 2px solid #004d75; padding-bottom: 12px; margin-bottom: 20px; }
        .logo-row { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #004d75; color: white; padding: 6px 8px; text-align: left; font-size: 10px; }
        th.right { text-align: right; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5eeff; font-size: 10px; }
        td.right { text-align: right; }
        tr:nth-child(even) { background: #f8f9ff; }
        .badge-selesai  { background: #d3f4e0; color: #1a6b3a; padding: 2px 6px; border-radius: 99px; font-size: 9px; font-weight: 700; }
        .badge-sebagian { background: #ffecc9; color: #8b5e00; padding: 2px 6px; border-radius: 99px; font-size: 9px; font-weight: 700; }
        .badge-belum    { background: #ffdad6; color: #93000a; padding: 2px 6px; border-radius: 99px; font-size: 9px; font-weight: 700; }
        .footer { margin-top: 20px; font-size: 9px; color: #707880; text-align: center; border-top: 1px solid #c0c7d0; padding-top: 8px; }
    </style>
</head>
<body>
<div class="header">
    <h1>REKAP REKONSILIASI PERUBAHAN HARGA KOMODITAS</h1>
    <h2>Periode: {{ $periode?->nama }} — Provinsi Sulawesi Utara</h2>
    <div style="font-size:10px;color:#40484f">Dicetak: {{ now()->format('d M Y, H:i') }} WIB &bull; BPS Provinsi Sulawesi Utara — Divisi Distribusi Statistik</div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kabupaten/Kota</th>
            <th class="right">Komoditas Perlu Alasan</th>
            <th class="right">Alasan Terisi</th>
            <th class="right">Progress</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($statusWilayah as $i => $s)
        @php
            $status = match(true) {
                $s['persen'] >= 100 => 'selesai',
                $s['persen'] > 0    => 'sebagian',
                default              => 'belum',
            };
            $statusLabel = match($status) {
                'selesai' => 'Selesai', 'sebagian' => 'Sebagian', default => 'Belum',
            };
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $s['wilayah']->tipe === 'kota' ? 'Kota' : 'Kab.' }} {{ $s['wilayah']->nama_wilayah }}</td>
            <td class="right">{{ $s['perlu'] }}</td>
            <td class="right">{{ $s['sudah'] }}</td>
            <td class="right"><strong>{{ $s['persen'] }}%</strong></td>
            <td><span class="badge-{{ $status }}">{{ $statusLabel }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:16px;padding:10px;background:#f8f9ff;border:1px solid #c0c7d0;border-radius:4px;">
    <strong>Ringkasan:</strong>
    Selesai: {{ collect($statusWilayah)->filter(fn($s) => $s['persen'] >= 100)->count() }} wilayah &bull;
    Sebagian: {{ collect($statusWilayah)->filter(fn($s) => $s['persen'] > 0 && $s['persen'] < 100)->count() }} wilayah &bull;
    Belum: {{ collect($statusWilayah)->filter(fn($s) => $s['persen'] === 0)->count() }} wilayah
</div>

<div class="footer">
    Dokumen ini digenerate otomatis oleh Sistem Manajemen Perubahan Harga Komoditas SULUT
</div>
</body>
</html>
