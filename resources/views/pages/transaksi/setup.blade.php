@extends('layouts.app')

@section('title', 'Setup - ' . $transaction->no_referensi)

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="bastSetup()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <div class="flex items-center space-x-4">
            <div class="p-3.5 rounded-2xl bg-primary-50 text-primary-600 dark:bg-navy-950/30 dark:text-primary-400 shadow-inner border border-primary-100/50 dark:border-primary-900/30">
                <i class="fas fa-file-invoice text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Setup Dokumen</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Lengkapi format Berita Acara Serah Terima Barang untuk No. Ref: <span class="font-mono font-bold text-primary-600">{{ $transaction->no_referensi }}</span></p>
            </div>
        </div>
        <a href="{{ route('barang-' . $transaction->jenis . '.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-150 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition-all text-xs border border-gray-200/50 dark:border-gray-700/50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 px-4 py-3.5 rounded-2xl flex items-start gap-3 shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
        <div>
            <strong class="font-bold block">Gagal menyimpan!</strong>
            <span class="text-sm mt-0.5 block">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <form action="{{ route('transaksi.bast.save', $transaction->id) }}" method="POST" target="_blank" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        
        <!-- Left Panel: Metadata (Col span 5) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6 space-y-5">
                <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                    <i class="fas fa-info-circle text-primary-500 text-sm"></i>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Informasi Berita Acara</h2>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Nomor Berita Acara <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_berita_acara" x-model="nomorBaInput" required 
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white font-bold outline-none focus:ring-2 focus:ring-primary-500 transition-all" 
                           placeholder="Contoh: 300.2.2/BA.012/Darlog/2026">
                    <p class="text-[10px] text-gray-400">Silakan masukkan format nomor Berita Acara secara manual dan lengkap.</p>
                </div>

                <!-- Mode Tanggal -->
                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Mode Tanggal Dokumen</label>
                    <div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-navy-950 p-1 rounded-xl border border-gray-200 dark:border-gray-700">
                        <button type="button" @click="setModeTanggal('otomatis')" 
                                :class="modeTanggal === 'otomatis' ? 'bg-primary-600 text-white font-bold' : 'text-gray-500 dark:text-gray-400'"
                                class="py-2 text-xs rounded-lg transition-all">
                            Otomatis (Sistem)
                        </button>
                        <button type="button" @click="setModeTanggal('manual')" 
                                :class="modeTanggal === 'manual' ? 'bg-primary-600 text-white font-bold' : 'text-gray-500 dark:text-gray-400'"
                                class="py-2 text-xs rounded-lg transition-all">
                            Manual (Input Sendiri)
                        </button>
                    </div>
                    <input type="hidden" name="mode_tanggal" :value="modeTanggal">
                </div>

                <!-- Hari, Tanggal, Bulan, Tahun -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Hari</label>
                        <input type="text" name="hari" x-model="hari" :readonly="modeTanggal === 'otomatis'"
                               :class="modeTanggal === 'otomatis' ? 'bg-gray-100 dark:bg-navy-800 text-gray-500' : 'bg-gray-50 dark:bg-navy-950 text-gray-900 dark:text-white'"
                               class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm outline-none transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Tanggal (Terbilang)</label>
                        <input type="text" name="tanggal" x-model="tanggal" :readonly="modeTanggal === 'otomatis'"
                               :class="modeTanggal === 'otomatis' ? 'bg-gray-100 dark:bg-navy-800 text-gray-500' : 'bg-gray-50 dark:bg-navy-950 text-gray-900 dark:text-white'"
                               class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm outline-none transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Bulan</label>
                        <input type="text" name="bulan" x-model="bulan" :readonly="modeTanggal === 'otomatis'"
                               :class="modeTanggal === 'otomatis' ? 'bg-gray-100 dark:bg-navy-800 text-gray-500' : 'bg-gray-50 dark:bg-navy-950 text-gray-900 dark:text-white'"
                               class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm outline-none transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Tahun (Terbilang)</label>
                        <input type="text" name="tahun" x-model="tahun" :readonly="modeTanggal === 'otomatis'"
                               :class="modeTanggal === 'otomatis' ? 'bg-gray-100 dark:bg-navy-800 text-gray-500' : 'bg-gray-50 dark:bg-navy-950 text-gray-900 dark:text-white'"
                               class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm outline-none transition-all">
                    </div>
                </div>

                @if($transaction->jenis == 'keluar')
                <!-- Lokasi Penyerahan (hanya untuk Barang Keluar) -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Kecamatan Penyerahan</label>
                        <input type="text" name="kecamatan" x-model="kecamatan" placeholder="Contoh: Singaparna"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Desa Penyerahan</label>
                        <input type="text" name="desa" x-model="desa" placeholder="Contoh: Cikunten"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                    </div>
                </div>
                @endif

                <!-- Catatan Tambahan (Keperluan) -->
                <div class="flex flex-col gap-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Catatan Tambahan / Keperluan</label>
                    <textarea name="catatan" rows="3" x-model="catatan" placeholder="Contoh: bantuan darurat logistik korban tanah longsor"
                              class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all"></textarea>
                </div>
            </div>

            <!-- Card: Pihak Terkait Info -->
            <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6 space-y-4">
                <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                    <i class="fas fa-user-shield text-primary-500 text-sm"></i>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Pihak Kesatu</h2>
                </div>
                <div class="space-y-4 text-xs">

                    {{-- ===== PIHAK PERTAMA (MANUAL INPUT) ===== --}}
                    <div class="p-3 bg-amber-50/60 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/50 rounded-xl space-y-2">
                        <div class="text-[10px] text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider flex items-center gap-1">
                            <i class="fas fa-pen-to-square text-[9px]"></i> Pihak Pertama (Yang Menyerahkan) — Isi Manual
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-500 font-semibold">Nama Lengkap</label>
                            <input type="text" name="penyerah_nama" x-model="penyerahNama"
                                   class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                   placeholder="Nama lengkap pihak yang menyerahkan">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-500 font-semibold">NIP / Nomor Identitas</label>
                            <input type="text" name="penyerah_nip" x-model="penyerahNip"
                                   class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                   placeholder="NIP atau nomor identitas (kosongkan jika tidak ada)">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-500 font-semibold">Jabatan</label>
                            <input type="text" name="penyerah_jabatan" x-model="penyerahJabatan"
                                   class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                   placeholder="Jabatan / pangkat">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] text-gray-500 font-semibold">Alamat / Instansi</label>
                            <input type="text" name="penyerah_alamat" x-model="penyerahAlamat"
                                   class="w-full px-3 py-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-lg text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                   placeholder="Alamat atau nama instansi">
                        </div>
                    </div>

                    {{-- ===== PIHAK KEDUA (AUTO DARI SISTEM) ===== --}}
                    @if($transaction->jenis === 'masuk')
                    <div class="p-3 bg-gray-50/50 dark:bg-navy-950/50 border border-gray-100 dark:border-gray-800 rounded-xl">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Pihak Kedua (Yang Menerima)</div>
                        <div class="text-sm font-bold text-gray-800 dark:text-white mt-1">{{ $transaction->penerima->nama_lengkap ?? $transaction->penerima->name ?? 'Petugas BPBD' }}</div>
                        <div class="text-gray-500 mt-0.5">NIP: {{ $transaction->penerima->nip ?? '-' }}</div>
                        <div class="text-gray-500">Jabatan: {{ $transaction->penerima->jabatan->nama_jabatan ?? 'Staf BPBD' }}</div>
                    </div>
                    @else
                    <div class="p-3 bg-gray-50/50 dark:bg-navy-950/50 border border-gray-100 dark:border-gray-800 rounded-xl">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Pihak Kedua (Yang Menerima)</div>
                        <div class="text-sm font-bold text-gray-800 dark:text-white mt-1">{{ $transaction->pihakKedua->nama_pihak ?? $transaction->penerima_penyerah }}</div>
                        <div class="text-gray-500 mt-0.5">NIP: {{ $transaction->pihakKedua->nip ?? '-' }}</div>
                        <div class="text-gray-500">Alamat/Instansi: {{ $transaction->pihakKedua->instansi ?? $transaction->penerima_penyerah }}</div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Right Panel: Barang & Cetak (Col span 7) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-150 dark:border-gray-800 shadow-sm p-6 space-y-5">
                <div class="flex items-center border-b border-gray-50 dark:border-gray-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-boxes-stacked text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Daftar Barang</h2>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-100 dark:border-gray-800 rounded-2xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-navy-800">
                            <tr>
                                <th class="px-4 py-3 font-bold text-gray-500">No</th>
                                <th class="px-4 py-3 font-bold text-gray-500">Nama Barang</th>
                                <th class="px-4 py-3 font-bold text-gray-500 text-center w-24">Banyaknya</th>
                                <th class="px-4 py-3 font-bold text-gray-500">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-850">
                            <template x-for="(item, index) in selectedItems" :key="item.barang_id">
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-navy-950/20">
                                    <td class="px-4 py-3 font-medium text-gray-400" x-text="index + 1"></td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5" x-text="item.kode_barang"></div>
                                        <input type="hidden" :name="`items[${index}][barang_id]`" :value="item.barang_id">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-1">
                                            <span class="font-bold text-gray-900 dark:text-white text-xs" x-text="item.jumlah_barang_kecil"></span>
                                            <span class="text-gray-400 text-xs" x-text="item.satuan"></span>
                                        </div>
                                        <input type="hidden" :name="`items[${index}][jumlah_barang_kecil]`" :value="item.jumlah_barang_kecil">
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 italic" x-text="item.keterangan || '-'"></span>
                                        <input type="hidden" :name="`items[${index}][keterangan]`" :value="item.keterangan">
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="selectedItems.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">Belum ada barang dipilih. Silakan tambah barang.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Action Button -->
                <div class="pt-4 flex justify-end space-x-3 border-t border-gray-50 dark:border-gray-800">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/25 transition-all text-xs gap-2">
                        <i class="fas fa-print"></i> Simpan & Cetak BAST PDF
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Tambah Barang -->
    <x-modal title="Tambah Barang Ke BAST" x-show="openAddModal">
        <div class="space-y-4">
            <div class="flex flex-col gap-1.5" x-data="{ searchQuery: '' }">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Cari Barang</label>
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari berdasarkan nama atau kode..."
                           class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                    <div class="mt-2 border rounded-xl max-h-48 overflow-y-auto divide-y divide-gray-150 dark:divide-gray-800 bg-white dark:bg-navy-950">
                        <template x-for="item in itemsList.filter(i => !searchQuery || i.nama_barang.toLowerCase().includes(searchQuery.toLowerCase()) || i.kode_barang.toLowerCase().includes(searchQuery.toLowerCase()))" :key="item.id">
                            <button type="button" @click="addItem(item)" class="w-full text-left px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-navy-900/50 flex justify-between items-center text-xs">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></span>
                                    <span class="text-gray-400 block font-mono text-[10px]" x-text="item.kode_barang"></span>
                                </div>
                                <span class="bg-primary-50 text-primary-600 px-2 py-0.5 rounded font-bold uppercase text-[9px]" x-text="item.satuan"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" @click="openAddModal = false" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-xs">Tutup</button>
            </div>
        </div>
    </x-modal>
</div>

<script>
function bastSetup() {
    return {
        openAddModal: false,
        nomorBaInput: '{{ $transaction->nomor_berita_acara ?? "300.2.2/BA.            /Darlog/2026" }}',
        penyerahNama: '{{ old("penyerah_nama", $transaction->penyerah_nama
            ?? ($transaction->jenis === "masuk"
                ? $transaction->penerima_penyerah
                : ($transaction->pihakKesatu->nama_pihak ?? ""))) }}',
        penyerahNip: '{{ old("penyerah_nip", $transaction->penyerah_nip
            ?? ($transaction->jenis === "masuk"
                ? ""
                : ($transaction->pihakKesatu->nip ?? ""))) }}',
        penyerahJabatan: '{{ old("penyerah_jabatan", $transaction->penyerah_jabatan
            ?? ($transaction->jenis === "masuk"
                ? "Penyedia / Pengirim Barang"
                : ($transaction->pihakKesatu->jabatan ?? ""))) }}',
        penyerahAlamat: '{{ old("penyerah_alamat", $transaction->penyerah_alamat
            ?? ($transaction->jenis === "masuk"
                ? ""
                : ($transaction->pihakKesatu->instansi ?? ($transaction->pihakKesatu->alamat ?? "")))) }}',
        modeTanggal: '{{ $transaction->mode_tanggal ?? "otomatis" }}',
        hari: '{{ $transaction->hari ?? "" }}',
        tanggal: '{{ $transaction->tanggal ?? "" }}',
        bulan: '{{ $transaction->bulan ?? "" }}',
        tahun: '{{ $transaction->tahun ?? "" }}',
        kecamatan: '{{ $transaction->kecamatan ?? "" }}',
        desa: '{{ $transaction->desa ?? "" }}',
        catatan: '{{ $transaction->catatan ?? $transaction->keterangan }}',
        
        selectedItems: [
            @foreach($transactions as $tx)
            {
                barang_id: {{ $tx->barang_id }},
                nama_barang: '{{ $tx->barang->nama_barang }}',
                kode_barang: '{{ $tx->barang->kode_barang }}',
                jumlah_barang_kecil: {{ $tx->jumlah_barang_kecil }},
                satuan: '{{ $tx->barang->satuanKecil->nama_satuan ?? "Pcs" }}',
                keterangan: '{{ $tx->keterangan ?? "" }}'
            },
            @endforeach
        ],
        
        itemsList: [
            @foreach($items as $item)
            {
                id: {{ $item->id }},
                nama_barang: '{{ $item->nama_barang }}',
                kode_barang: '{{ $item->kode_barang }}',
                satuan: '{{ $item->satuanKecil->nama_satuan ?? "Pcs" }}'
            },
            @endforeach
        ],
        
        init() {
            if (this.modeTanggal === 'otomatis') {
                this.updateAutoDate();
            }
        },
        
        setModeTanggal(mode) {
            this.modeTanggal = mode;
            if (mode === 'otomatis') {
                this.updateAutoDate();
            }
        },
        
        updateAutoDate() {
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const now = new Date();
            this.hari = days[now.getDay()];
            this.tanggal = this.numberToWords(now.getDate());
            this.bulan = months[now.getMonth()];
            this.tahun = this.yearToWords(now.getFullYear());
        },
        
        addItem(item) {
            // Check if already selected
            const exists = this.selectedItems.some(i => i.barang_id === item.id);
            if (exists) {
                alert('Barang ini sudah ada di daftar.');
                return;
            }
            this.selectedItems.push({
                barang_id: item.id,
                nama_barang: item.nama_barang,
                kode_barang: item.kode_barang,
                jumlah_barang_kecil: 1,
                satuan: item.satuan,
                keterangan: ''
            });
            this.openAddModal = false;
        },
        
        removeItem(index) {
            this.selectedItems.splice(index, 1);
        },
        
        numberToWords(num) {
            const words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
            if (num <= 11) return words[num];
            if (num < 20) return this.numberToWords(num - 10) + ' Belas';
            if (num < 100) {
                const principal = Math.floor(num / 10);
                const remainder = num % 10;
                return words[principal] + ' Puluh' + (remainder ? ' ' + this.numberToWords(remainder) : '');
            }
            return String(num);
        },
        
        yearToWords(year) {
            if (year >= 2000 && year < 2100) {
                const tens = year - 2000;
                return 'Dua Ribu' + (tens ? ' ' + this.numberToWords(tens) : '');
            }
            return String(year);
        }
    }
}
</script>
@endsection
