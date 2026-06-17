<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Serah Terima - {{ $transaction->no_referensi }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .container { padding: 20px 40px; }
        
        /* Header */
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; position: relative; }
        .header img { position: absolute; left: 0; top: 0; width: 80px; }
        .header h2 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 0; font-size: 10pt; }
        
        /* Title */
        .title-box { text-align: center; margin-bottom: 20px; }
        .title-box h3 { margin: 0; text-decoration: underline; text-transform: uppercase; font-size: 13pt; }
        .title-box p { margin: 0; font-size: 11pt; }
        
        /* Content */
        .content { margin-bottom: 20px; text-align: justify; }
        .parties { margin-bottom: 20px; }
        .party-row { margin-bottom: 10px; }
        .party-label { display: inline-block; width: 100px; vertical-align: top; }
        .party-dots { display: inline-block; width: 10px; vertical-align: top; }
        .party-value { display: inline-block; width: 450px; vertical-align: top; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px 10px; font-size: 11pt; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
        
        /* Signature */
        .signature-section { margin-top: 40px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; width: 50%; padding: 0; text-align: center; }
        .signature-box { margin-top: 60px; font-weight: bold; }
        .signature-name { text-decoration: underline; }
        
        .footer-note { margin-top: 15px; text-align: center; font-style: italic; font-size: 10pt; }
        
        .know-section { margin-top: 40px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('img/logo-daerah.png') }}" onerror="this.style.display='none'">
            <h2>Pemerintah Daerah Kabupaten Tasikmalaya</h2>
            <h1>Badan Penanggulangan Bencana Daerah</h1>
            <p>Jl. Otto Iskandardinata No. 19 Tasikmalaya Telp dan Fax (0265) 334111</p>
            <p>Email: bpbd@tasikmalayakab.go.id</p>
            <p>TASIKMALAYA - 46113</p>
        </div>

        <!-- Title -->
        <div class="title-box">
            <h3>Berita Acara Serah Terima Barang</h3>
            <p>Nomor: {{ $transaction->referenceBap?->nomor_ba ?? $transaction->no_referensi }}</p>
            @if($transaction->referenceBap)
                <p style="font-size: 10pt; margin-top: 2px;">Referensi BAP: {{ $transaction->referenceBap->judul_ba }} ({{ \Carbon\Carbon::parse($transaction->referenceBap->tgl_ba)->format('d/m/Y') }})</p>
            @endif
        </div>

        <!-- Opening -->
        <div class="content">
            Pada hari ini, {{ $transaction->tgl_transaksi->translatedFormat('l') }} 
            Tanggal {{ $transaction->tgl_transaksi->format('d') }} 
            Bulan {{ $transaction->tgl_transaksi->translatedFormat('F') }} 
            Tahun {{ $transaction->tgl_transaksi->format('Y') }}
            yang bertanda tangan dibawah ini:
        </div>

        <!-- Parties -->
        <div class="parties">
            <div class="party-row">
                <div class="party-label">I. Nama</div>
                <div class="party-dots">:</div>
                <div class="party-value"><strong>{{ $transaction->pihakKesatu->nama_pihak ?? '................................' }}</strong></div>
            </div>
            <div class="party-row">
                <div class="party-label">&nbsp;&nbsp;&nbsp;NIP</div>
                <div class="party-dots">:</div>
                <div class="party-value">{{ $transaction->pihakKesatu->nip ?? '................................' }}</div>
            </div>
            <div class="party-row">
                <div class="party-label">&nbsp;&nbsp;&nbsp;Jabatan</div>
                <div class="party-dots">:</div>
                <div class="party-value">{{ $transaction->pihakKesatu->jabatan ?? '................................' }}</div>
            </div>
            <div style="margin-left: 115px; margin-top: 5px;">Selanjutnya disebut <strong>PIHAK PERTAMA</strong></div>
            
            <div class="party-row" style="margin-top: 15px;">
                <div class="party-label">II. Nama</div>
                <div class="party-dots">:</div>
                <div class="party-value"><strong>{{ $transaction->pihakKedua->nama_pihak ?? '................................' }}</strong></div>
            </div>
            <div class="party-row">
                <div class="party-label">&nbsp;&nbsp;&nbsp;NIP</div>
                <div class="party-dots">:</div>
                <div class="party-value">{{ $transaction->pihakKedua->nip ?? '-' }}</div>
            </div>
            <div class="party-row">
                <div class="party-label">&nbsp;&nbsp;&nbsp;Jabatan</div>
                <div class="party-dots">:</div>
                <div class="party-value">{{ $transaction->pihakKedua->jabatan ?? '-' }}</div>
            </div>
            <div class="party-row">
                <div class="party-label">&nbsp;&nbsp;&nbsp;Alamat</div>
                <div class="party-dots">:</div>
                <div class="party-value">{{ $transaction->pihakKedua->instansi ?? '................................' }}</div>
            </div>
            <div style="margin-left: 115px; margin-top: 5px;">Selanjutnya disebut <strong>PIHAK KEDUA</strong></div>
        </div>

        <div class="content">
            Dengan ini <strong>PIHAK PERTAMA</strong> menyerahkan bantuan kepada <strong>PIHAK KEDUA</strong>, berupa:
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Nama Barang</th>
                    <th width="150">Banyaknya</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $tx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $tx->barang->nama_barang }}</td>
                    <td class="text-center">
                        @if($tx->jumlah_barang_besar > 0)
                            {{ number_format($tx->jumlah_barang_besar) }} {{ $tx->barang->satuanBesar->nama_satuan ?? 'Pcs' }}
                            @if($tx->jumlah_barang_kecil > 0)
                                & {{ number_format($tx->jumlah_barang_kecil) }} {{ $tx->barang->satuanKecil->nama_satuan ?? 'Pcs' }}
                            @endif
                        @else
                            {{ number_format($tx->jumlah_barang_kecil) }} {{ $tx->barang->satuanKecil->nama_satuan ?? 'Pcs' }}
                        @endif
                    </td>
                    <td>{{ $tx->keterangan }}</td>
                </tr>
                @endforeach
                @for($i = count($transactions); $i < 10; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="content">
            PIHAK KEDUA menerima bantuan sebagaimana tertulis diatas dari PIHAK PERTAMA untuk ............................................................................................................................................. di Desa ........................................................... Kecamatan ...........................................................
        </div>

        <div class="content">
            Demikian berita acara ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <p>Yang Menerima</p>
                        <p><strong>PIHAK KEDUA,</strong></p>
                        <div class="signature-box">
                            <span class="signature-name">{{ $transaction->pihakKedua->nama_pihak ?? '................................' }}</span>
                        </div>
                    </td>
                    <td>
                        <p>Yang Menyerahkan</p>
                        <p><strong>PIHAK PERTAMA,</strong></p>
                        <div class="signature-box">
                            <span class="signature-name">{{ $transaction->pihakKesatu->nama_pihak ?? '................................' }}</span><br>
                            @if($transaction->pihakKesatu && $transaction->pihakKesatu->nip)
                            NIP. {{ $transaction->pihakKesatu->nip }}
                            @else
                            NIP. ................................
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="know-section">
            <p>Mengetahui,</p>
            <p>Kepala Pelaksana</p>
            <p>BPBD Kabupaten Tasikmalaya</p>
            <div class="signature-box" style="margin-top: 60px;">
                <span class="signature-name">RONI, A.Ks., M.M</span><br>
                NIP. 19690901 199303 1 004
            </div>
        </div>
    </div>
</body>
</html>
