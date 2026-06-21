{{--
    Partial: KOP Surat Resmi BPBD Kab. Tasikmalaya
    Usage:
        @include('reports.partials.kop', ['color' => '#1e40af', 'title' => 'LAPORAN ...'])
    Parameters:
        $color   : accent color (hex)  — default #1e3a5f
        $logoB64 : base64 encoded logo — passed from controller
--}}
@php
    $kopColor  = $color  ?? '#1e3a5f';
    $logoSrc   = null;
    $logoPath  = public_path('img/logo-daerah.png');
    if (file_exists($logoPath)) {
        $ext      = pathinfo($logoPath, PATHINFO_EXTENSION);
        $mime     = $ext === 'png' ? 'image/png' : 'image/jpeg';
        $logoSrc  = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<div class="kop-wrapper">
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="kop-logo" alt="Logo BPBD">
                @else
                    <div class="kop-logo-placeholder">
                        <div class="logo-circle">
                            <span style="font-size:22pt; color:#fff; font-weight:bold;">⚙</span>
                        </div>
                    </div>
                @endif
            </td>
            <td class="kop-text-cell">
                <div class="kop-instansi-atas">PEMERINTAH DAERAH KABUPATEN TASIKMALAYA</div>
                <div class="kop-instansi-nama">BADAN PENANGGULANGAN BENCANA DAERAH</div>
                <div class="kop-instansi-info">Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111</div>
                <div class="kop-instansi-info">Email: bpbd@tasikmalayakab.go.id &nbsp;|&nbsp; TASIKMALAYA - 46113</div>
            </td>
        </tr>
    </table>
    <div class="kop-divider" style="border-top: 4px solid {{ $kopColor }}; border-bottom: 1px solid {{ $kopColor }}; margin-top: 6px; height: 2px;"></div>
</div>
