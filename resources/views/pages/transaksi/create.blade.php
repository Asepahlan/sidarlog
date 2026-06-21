@extends('layouts.app')

@section('title', 'Tambah Transaksi ' . ($jenis == 'masuk' ? 'Barang Masuk' : 'Barang Keluar'))

@section('content')
@php
    $itemsData = $items->map(function($item) use ($warehouses) {
        $stocks = [];
        foreach ($warehouses as $wh) {
            $stocks[$wh->id] = [
                'kecil' => $item->getStockKecilInWarehouse($wh->id),
                'besar' => $item->satuanBesar ? $item->getStockBesarInWarehouse($wh->id) : 0,
            ];
        }
        return [
            'id' => $item->id,
            'nama_barang' => $item->nama_barang,
            'kode_barang' => $item->kode_barang,
            'satuan_kecil' => $item->satuanKecil->nama_satuan ?? 'Pcs',
            'satuan_besar' => $item->satuanBesar->nama_satuan ?? null,
            'stocks' => $stocks
        ];
    });
@endphp

<div class="max-w-6xl" x-data="{ 
    openQuickAddParty: false,
    newParty: { nama_pihak: '', instansi: '', jabatan: '', alamat: '', no_telp: '' },
    isSubmitting: false,
    errorMessage: '',
    successMessage: '',
    mainFormError: '',
    selectedPihakKedua: '{{ old('pihak_kedua_id') ?? '' }}',
    penerimaLainnya: '{{ old('penerima_penyerah') ?? '' }}',
    selectedItemId: '{{ old('barang_id') ?? '' }}',
    selectedWarehouseId: '{{ old('gudang_id', $warehouses->first()->id ?? '') }}',
    jumlahKecil: {{ old('jumlah_barang_kecil') ?? 0 }},
    jumlahBesar: {{ old('jumlah_barang_besar') ?? 0 }},
    items: {{ json_encode($itemsData) }},
    
    get selectedItem() {
        return this.items.find(i => i.id == this.selectedItemId) || null;
    },
    get currentStock() {
        if (!this.selectedItem || !this.selectedWarehouseId) return { kecil: 0, besar: 0 };
        return this.selectedItem.stocks[this.selectedWarehouseId] || { kecil: 0, besar: 0 };
    },
    get isStockInsufficient() {
        if ('{{ $jenis }}' !== 'keluar') return false;
        const stock = this.currentStock;
        return (this.jumlahKecil > stock.kecil) || (this.jumlahBesar > stock.besar);
    },
    init() {
        this.$watch('selectedPihakKedua', value => {
            if (value !== '') {
                this.penerimaLainnya = '';
            }
        });
    },
    submitQuickAddParty() {
        if (!this.newParty.nama_pihak) return;
        this.isSubmitting = true;
        this.errorMessage = '';
        this.successMessage = '';
        
        const payload = {
            nama_pihak: this.newParty.nama_pihak,
            jabatan: this.newParty.jabatan || '',
            instansi: this.newParty.instansi + (this.newParty.alamat ? ' (Alamat: ' + this.newParty.alamat + ')' : ''),
            nip: this.newParty.no_telp || ''
        };
        
        fetch('{{ route('pihak-kedua.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(res => {
            if (res.success) {
                const select = document.getElementById('pihak_kedua_select');
                const option = document.createElement('option');
                option.value = res.data.id;
                option.text = `${res.data.nama_pihak} - ${res.data.instansi || ''}`;
                option.selected = true;
                select.add(option);
                
                this.selectedPihakKedua = res.data.id;
                this.penerimaLainnya = '';
                
                this.newParty = { nama_pihak: '', instansi: '', jabatan: '', alamat: '', no_telp: '' };
                this.openQuickAddParty = false;
                
                this.successMessage = 'Pihak Kedua berhasil ditambahkan: ' + res.data.nama_pihak;
                setTimeout(() => { this.successMessage = ''; }, 5000);
            } else {
                this.errorMessage = res.message || 'Gagal menambahkan data.';
            }
        })
        .catch(err => {
            console.error(err);
            this.errorMessage = err.errors ? Object.values(err.errors).flat().join(', ') : 'Terjadi kesalahan sistem.';
        })
        .finally(() => {
            this.isSubmitting = false;
        });
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center space-x-4">
            <div class="p-3.5 rounded-2xl {{ $jenis == 'masuk' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-orange-50 text-orange-600 dark:bg-orange-950/30 dark:text-orange-400' }} shadow-inner border {{ $jenis == 'masuk' ? 'border-emerald-100/50 dark:border-emerald-900/30' : 'border-orange-100/50 dark:border-orange-900/30' }}">
                <i class="fas {{ $jenis == 'masuk' ? 'fa-arrow-down-long text-xl' : 'fa-arrow-up-long text-xl' }}"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Tambah Transaksi {{ $jenis == 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Input data mutasi barang {{ $jenis }} ke sistem secara akurat.</p>
            </div>
        </div>
        <a href="{{ route('barang-' . $jenis . '.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-150 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition-all text-xs border border-gray-200/50 dark:border-gray-700/50">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    <!-- Client-side Error Alert -->
    <div x-show="mainFormError" class="mb-6 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 px-4 py-3.5 rounded-2xl flex items-start gap-3 shadow-sm" role="alert" x-cloak>
        <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
        <div>
            <strong class="font-bold block">Gagal menyimpan!</strong>
            <span class="text-sm mt-0.5 block" x-text="mainFormError"></span>
        </div>
    </div>

    <!-- AJAX Success Alert -->
    <div x-show="successMessage" class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-2xl flex items-center gap-3 transition-all duration-300 shadow-sm" role="alert" x-cloak>
        <i class="fas fa-check-circle text-lg"></i>
        <span class="block sm:inline font-medium" x-text="successMessage"></span>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-400 px-4 py-3.5 rounded-2xl" role="alert">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <strong class="font-bold">Gagal menyimpan!</strong>
        </div>
        <ul class="mt-2 list-disc list-inside text-sm pl-2 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Main Form (Col span 2) -->
        <div class="lg:col-span-2">
            <form action="{{ route('barang-' . $jenis . '.store') }}" method="POST" 
                  @submit="
                      if (!selectedItemId) {
                          $event.preventDefault();
                          mainFormError = 'Barang wajib dipilih.';
                          window.scrollTo({top: 0, behavior: 'smooth'});
                      } else if ('{{ $jenis }}' === 'keluar' && !selectedPihakKedua && !penerimaLainnya) { 
                          $event.preventDefault(); 
                          mainFormError = 'Penerima wajib ditentukan (Pilih Pihak Kedua atau isi Nama Penerima Lainnya).'; 
                          window.scrollTo({top: 0, behavior: 'smooth'}); 
                      } else if ('{{ $jenis }}' === 'keluar' && isStockInsufficient) {
                          $event.preventDefault();
                          mainFormError = 'Stok di gudang tidak mencukupi untuk melakukan transaksi keluar ini.';
                          window.scrollTo({top: 0, behavior: 'smooth'});
                      }
                  " 
                  class="space-y-6">
                @csrf
                <input type="hidden" name="jenis" value="{{ $jenis }}">

                <!-- Card 1: Barang & Lokasi -->
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-box text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Pilih Barang & Gudang</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5" x-data="{ showDropdown: false, searchQuery: '' }" @click.away="showDropdown = false">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Barang <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <!-- Trigger Button -->
                                <button type="button" @click="showDropdown = !showDropdown" 
                                        class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-left text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                    <span x-text="selectedItem ? selectedItem.kode_barang + ' - ' + selectedItem.nama_barang : 'Pilih Barang'"></span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="showDropdown ? 'rotate-180' : ''"></i>
                                </button>

                                <!-- Hidden input to submit the selected ID -->
                                <input type="hidden" name="barang_id" :value="selectedItemId">

                                <!-- Dropdown List -->
                                <div x-show="showDropdown" x-cloak x-transition
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-navy-950 border border-gray-150 dark:border-gray-800 rounded-2xl shadow-xl max-h-64 overflow-hidden flex flex-col">
                                    
                                    <!-- Search Input -->
                                    <div class="p-2.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-navy-900/50 relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none text-gray-400">
                                            <i class="fas fa-search text-xs"></i>
                                        </span>
                                        <input type="text" x-model="searchQuery" placeholder="Cari nama atau kode barang..." 
                                               class="w-full pl-8 pr-4 py-2 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                                    </div>

                                    <!-- Options Container -->
                                    <div class="overflow-y-auto flex-1 max-h-48 divide-y divide-gray-50/50 dark:divide-gray-800/50">
                                        <!-- Loop -->
                                        <template x-for="item in items.filter(i => !searchQuery || i.nama_barang.toLowerCase().includes(searchQuery.toLowerCase()) || i.kode_barang.toLowerCase().includes(searchQuery.toLowerCase()))" :key="item.id">
                                            <button type="button" @click="selectedItemId = item.id; searchQuery = ''; showDropdown = false" 
                                                    class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-navy-900/50 transition-colors flex flex-col"
                                                    :class="selectedItemId == item.id ? 'bg-primary-50/50 dark:bg-primary-950/20' : ''">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white" x-text="item.nama_barang"></span>
                                                <span class="text-[10px] text-gray-500 font-mono mt-0.5" x-text="item.kode_barang"></span>
                                            </button>
                                        </template>
                                        <!-- No results -->
                                        <div x-show="items.filter(i => !searchQuery || i.nama_barang.toLowerCase().includes(searchQuery.toLowerCase()) || i.kode_barang.toLowerCase().includes(searchQuery.toLowerCase())).length === 0" class="text-center py-6 text-xs text-gray-400">
                                            <i class="fas fa-box-open mb-1.5 opacity-50 block text-lg"></i>
                                            Barang tidak ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Gudang <span class="text-red-500">*</span></label>
                            <select name="gudang_id" required x-model="selectedWarehouseId" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('gudang_id', $warehouses->first()->id ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->nama_gudang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Tanggal & Waktu Transaksi <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="tgl_transaksi" value="{{ old('tgl_transaksi', now()->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Jumlah Mutasi -->
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-calculator text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Jumlah Barang</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                                Jumlah (<span x-text="selectedItem ? selectedItem.satuan_kecil : 'Satuan Kecil'"></span>) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jumlah_barang_kecil" x-model.number="jumlahKecil" min="0" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                        </div>
                        <div class="flex flex-col gap-1.5" x-show="selectedItem && selectedItem.satuan_besar">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                                Jumlah (<span x-text="selectedItem ? selectedItem.satuan_besar : 'Satuan Besar'"></span>) <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="number" name="jumlah_barang_besar" x-model.number="jumlahBesar" min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pihak Terkait & Keterangan -->
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-handshake text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Pihak Terkait & Keterangan</h2>
                    </div>

                    @if($jenis == 'keluar')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Pihak Kesatu (BPBD)</label>
                            <select name="pihak_kesatu_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                <option value="">Pilih Pihak Pertama</option>
                                @foreach($firstParties as $party)
                                    <option value="{{ $party->id }}" {{ old('pihak_kesatu_id') == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} — {{ $party->jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Pihak Kedua (Penerima)</label>
                            <div class="flex items-stretch gap-2 w-full">
                                <select name="pihak_kedua_id" id="pihak_kedua_select" x-model="selectedPihakKedua" class="flex-grow min-w-0 px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                    <option value="">Pilih Pihak Kedua</option>
                                    @foreach($secondParties as $party)
                                        <option value="{{ $party->id }}" {{ old('pihak_kedua_id') == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} — {{ $party->instansi }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="openQuickAddParty = true" class="flex-shrink-0 px-3.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white font-bold transition-all shadow-md shadow-orange-500/20 flex items-center justify-center" title="Quick Add Pihak Kedua">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                            {{ $jenis == 'masuk' ? 'Nama Pengirim' : 'Nama Penerima (Lainnya)' }}
                        </label>
                        <input type="text" name="penerima_penyerah" x-model="penerimaLainnya" :disabled="selectedPihakKedua !== ''" 
                               class="w-full px-4 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" 
                               :class="selectedPihakKedua !== '' ? 'bg-gray-150 dark:bg-navy-950 cursor-not-allowed text-gray-400 border-gray-200 dark:border-gray-700' : 'bg-gray-50 dark:bg-navy-950 border-gray-200 dark:border-gray-700 dark:text-white'" 
                               :placeholder="selectedPihakKedua !== '' ? 'Dinonaktifkan karena penerima dipilih dari daftar.' : 'Masukkan nama personil/pihak terkait'">
                        @if($jenis == 'keluar')
                            <p class="text-[10px] text-gray-400 mt-1">
                                * Gunakan kolom ini hanya jika penerima tidak terdaftar di pilihan Pihak Kedua.
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-6 hidden">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor Berita Acara (BAP)</label>
                            <select name="reference_bap_id" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary-500 outline-none">
                                <option value="">— Tanpa BAP —</option>
                                @foreach($baps as $bap)
                                    <option value="{{ $bap->id }}" {{ old('reference_bap_id') == $bap->id ? 'selected' : '' }}>
                                        {{ $bap->nomor_ba }} — {{ $bap->judul_ba }} ({{ \Carbon\Carbon::parse($bap->tgl_ba)->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all" placeholder="Catatan tambahan untuk transaksi ini...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('barang-' . $jenis . '.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-2xl transition-all text-xs border border-gray-200/50 dark:border-gray-700/50">Batal</a>
                    <button type="submit" 
                            class="px-8 py-3 font-bold rounded-2xl text-white shadow-lg transition-all transform active:scale-95 text-xs flex items-center gap-2"
                            :class="isStockInsufficient ? 'bg-gray-400 cursor-not-allowed shadow-none' : '{{ $jenis == 'masuk' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30' : 'bg-orange-600 hover:bg-orange-700 shadow-orange-500/30' }}'">
                        <i class="fas fa-save"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar / Context info (Col span 1) -->
        <div class="space-y-6">
            <!-- Card: Stock Info -->
            <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-4">
                <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                    <i class="fas fa-info-circle text-primary-500 text-sm"></i>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Status Stok Gudang</h2>
                </div>

                <!-- Placeholder when no item selected -->
                <div x-show="!selectedItemId" class="text-center py-8 text-gray-400 space-y-2" x-transition>
                    <i class="fas fa-box-open text-4xl opacity-30"></i>
                    <p class="text-xs max-w-[200px] mx-auto leading-relaxed">Silakan pilih barang terlebih dahulu untuk melihat ketersediaan stok.</p>
                </div>

                <!-- Real-time Stock Info Widget -->
                <div x-show="selectedItemId" class="space-y-4" x-transition x-cloak>
                    <div class="p-4 bg-gray-50/50 dark:bg-navy-950/50 rounded-2xl border border-gray-100/50 dark:border-gray-800/50">
                        <div class="text-[10px] uppercase font-bold text-gray-400">Barang Terpilih</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-1" x-text="selectedItem ? selectedItem.nama_barang : ''"></div>
                        <div class="text-xs font-mono text-primary-600 mt-0.5" x-text="selectedItem ? selectedItem.kode_barang : ''"></div>
                    </div>

                    <!-- Warehouse Stock Display -->
                    <div class="space-y-3">
                        <div class="text-[10px] uppercase font-bold text-gray-400">Persediaan di Gudang</div>
                        
                        <!-- Small Unit Stock -->
                        <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-navy-950 rounded-2xl">
                            <div>
                                <span class="text-xs font-medium text-gray-500">Satuan Kecil</span>
                                <div class="text-xs font-bold text-gray-800 dark:text-white mt-0.5" x-text="selectedItem ? selectedItem.satuan_kecil : ''"></div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium text-gray-500">Stok</span>
                                <div class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5" x-text="currentStock.kecil"></div>
                            </div>
                        </div>

                        <!-- Large Unit Stock -->
                        <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-navy-950 rounded-2xl" x-show="selectedItem && selectedItem.satuan_besar">
                            <div>
                                <span class="text-xs font-medium text-gray-500">Satuan Besar</span>
                                <div class="text-xs font-bold text-gray-800 dark:text-white mt-0.5" x-text="selectedItem ? selectedItem.satuan_besar : ''"></div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-medium text-gray-500">Stok</span>
                                <div class="text-lg font-extrabold text-purple-600 dark:text-purple-400 mt-0.5" x-text="currentStock.besar"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Outbound specific validation preview -->
                    @if($jenis == 'keluar')
                    <div class="pt-3 space-y-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="text-[10px] uppercase font-bold text-gray-400">Rencana Pengeluaran</div>
                        
                        <div class="space-y-2">
                            <!-- Remaining Small Unit Preview -->
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Sisa Satuan Kecil</span>
                                <div class="font-bold" :class="currentStock.kecil - jumlahKecil < 0 ? 'text-red-500' : 'text-gray-800 dark:text-white'">
                                    <span x-text="currentStock.kecil"></span> 
                                    <span class="mx-1">→</span>
                                    <span x-text="currentStock.kecil - jumlahKecil"></span>
                                </div>
                            </div>

                            <!-- Remaining Large Unit Preview -->
                            <div class="flex justify-between items-center text-xs" x-show="selectedItem && selectedItem.satuan_besar">
                                <span class="text-gray-500">Sisa Satuan Besar</span>
                                <div class="font-bold" :class="currentStock.besar - jumlahBesar < 0 ? 'text-red-500' : 'text-gray-800 dark:text-white'">
                                    <span x-text="currentStock.besar"></span> 
                                    <span class="mx-1">→</span>
                                    <span x-text="currentStock.besar - jumlahBesar"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Warnings -->
                        <div x-show="isStockInsufficient" class="p-3.5 bg-red-50 dark:bg-red-950/30 border border-red-100 dark:border-red-900 rounded-2xl text-red-700 dark:text-red-400 text-xs flex items-start gap-2" x-transition>
                            <i class="fas fa-circle-exclamation mt-0.5 text-sm"></i>
                            <div>
                                <span class="font-bold block">Stok Tidak Cukup!</span>
                                Jumlah pengeluaran melebihi stok yang tersedia di gudang terpilih.
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card: Guidelines -->
            <div class="bg-gray-50/50 dark:bg-navy-950/30 border border-gray-100 dark:border-gray-800 rounded-3xl p-6 space-y-3.5">
                <h3 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest flex items-center gap-1.5">
                    <i class="fas fa-lightbulb text-amber-500"></i> Petunjuk Penggunaan
                </h3>
                <ul class="text-xs text-gray-500 dark:text-gray-400 space-y-2.5 leading-relaxed">
                    <li class="flex gap-2">
                        <span class="font-bold text-gray-700 dark:text-gray-300">•</span>
                        <span>Pilih **Barang** dan **Gudang** yang tepat agar penambahan/pengurangan stok tercatat pada lokasi fisik yang sesuai.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="font-bold text-gray-700 dark:text-gray-300">•</span>
                        <span>Input **Jumlah** pada kolom satuan (Kecil/Besar) sesuai transaksi fisik yang dilakukan. Kolom satuan besar otomatis dinonaktifkan/disembunyikan apabila barang terpilih tidak mendukung satuan besar.</span>
                    </li>
                    @if($jenis == 'keluar')
                    <li class="flex gap-2">
                        <span class="font-bold text-gray-700 dark:text-gray-300">•</span>
                        <span>Pastikan data **Pihak Kedua (Penerima)** sesuai untuk tanda tangan Berita Acara Serah Terima (BAST).</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal Quick Add Pihak Kedua -->
    <x-modal title="Tambah Pihak Kedua (Quick Add)" x-show="openQuickAddParty">
        <form @submit.prevent="submitQuickAddParty" class="space-y-4">
            <template x-if="errorMessage">
                <div class="bg-red-100/80 border border-red-400 text-red-700 px-4 py-2.5 rounded-xl text-sm">
                    <span x-text="errorMessage"></span>
                </div>
            </template>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                <input type="text" x-model="newParty.nama_pihak" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Instansi</label>
                    <input type="text" x-model="newParty.instansi" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                    <input type="text" x-model="newParty.jabatan" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Alamat</label>
                    <input type="text" x-model="newParty.alamat" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">No Telepon (Opsional)</label>
                    <input type="text" x-model="newParty.no_telp" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openQuickAddParty = false" :disabled="isSubmitting" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" :disabled="isSubmitting" class="px-6 py-2 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 flex items-center gap-2">
                    <span x-show="isSubmitting" class="animate-spin text-sm"><i class="fas fa-spinner"></i></span>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
