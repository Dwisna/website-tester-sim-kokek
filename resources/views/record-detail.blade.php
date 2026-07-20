@extends('layouts.app')

@section('title', 'Detail record')

@section('content')
<div class="wrap">
    <div class="page-header">
        <div>
            <h1 style="margin:0 0 6px;">Detail record</h1>
            <p class="text-muted" style="margin:0;">Ringkasan lengkap data hasil scraping dan integrasi dashboard.</p>
        </div>
        <div class="page-header-actions">
            @include('components.theme-toggle')
            <a class="btn-success" href="/">← Kembali ke dashboard</a>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-top:0;">{{ $record->nama_pekerjaan ?? 'Tanpa judul' }}</h2>
        <div class="detail-grid">
            <div><div class="field-label">Instansi</div><div class="field-value">{{ $record->nama_instansi ?? '-' }}</div></div>
            <div><div class="field-label">Tahun anggaran</div><div class="field-value">{{ $record->tahun_anggaran ?? '-' }}</div></div>
            <div><div class="field-label">Jenis pengadaan</div><div class="field-value">{{ $record->nama_jenis_pengadaan ?? '-' }}</div></div>
            <div><div class="field-label">Metode</div><div class="field-value">{{ $record->nama_metode_pengadaan ?? '-' }}</div></div>
            <div><div class="field-label">PIC</div><div class="field-value">{{ $record->pic ?? '-' }}</div></div>
            <div><div class="field-label">Lokasi</div><div class="field-value">{{ $record->lokasi_pekerjaan ?? '-' }}</div></div>
            <div><div class="field-label">Status penawaran</div><div class="field-value">{{ $record->is_status_kirim_penawaran ? 'Terkirim' : 'Belum' }}</div></div>
            <div><div class="field-label">Keterangan</div><div class="field-value">{{ $record->keterangan ?? '-' }}</div></div>
        </div>
    </div>
</div>
@endsection
