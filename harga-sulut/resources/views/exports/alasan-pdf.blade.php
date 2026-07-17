<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Alasan Perubahan Harga</title>
<style>body{font-family:'DejaVu Sans',sans-serif;font-size:9px;}table{width:100%;border-collapse:collapse;}th{background:#004d75;color:#fff;padding:4px 6px;}td{padding:4px 6px;border-bottom:1px solid #e5eeff;}tr:nth-child(even){background:#f8f9ff;}.header{text-align:center;margin-bottom:16px;border-bottom:2px solid #004d75;padding-bottom:10px;}</style>
</head><body>
<div class="header"><strong style="font-size:13px">TABEL ALASAN PERUBAHAN HARGA KOMODITAS</strong><br>Periode: {{ $periode?->nama }} — Sulawesi Utara<br>Dicetak: {{ now()->format('d M Y H:i') }}</div>
<table><thead><tr><th>No</th><th>Wilayah</th><th>Komoditas</th><th>Alasan</th><th>Faktor</th><th>Status</th></tr></thead><tbody>
@foreach($alasans as $i => $a)
<tr><td>{{ $i+1 }}</td><td>{{ $a->wilayah->nama_wilayah }}</td><td>{{ $a->komoditas->nama_komoditas }}</td><td>{{ Str::limit($a->alasan,100) }}</td><td>{{ $a->faktors_label }}</td><td>{{ $a->status_badge['label'] }}</td></tr>
@endforeach
</tbody></table>
</body></html>
