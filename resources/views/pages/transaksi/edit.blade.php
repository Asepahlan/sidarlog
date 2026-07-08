@extends('layouts.app')

@section('title', 'Edit Transaksi ' . ($jenis == 'masuk' ? 'Barang Masuk' : 'Barang Keluar'))

@section('content')
@php
    $itemsData = $items->map(function($item) use ($warehouses, $transaction) {
        $stocks = [];
        foreach ($warehouses as $wh) {
            $stocks[$wh->id] = [
                'kecil' => $item->getStockKecilInWarehouse($wh->id),
            ];
        }

        $totalStok = $item->stok_saat_ini_kecil ?? 0;
        // Revert the current transaction's quantity to let the user see total stock levels before this edit
        if ($item->id == $transaction->barang_id) {
            if ($transaction->jenis === 'keluar') {
                $totalStok += $transaction->jumlah_barang_kecil;
            } elseif ($transaction->jenis === 'masuk') {
                $totalStok = max(0, $totalStok - $transaction->jumlah_barang_kecil);
            }
        }

        return [
            'id' => $item->id,
            'nama_barang' => $item->nama_barang,
            'kode_barang' => $item->kode_barang,
            'satuan_kecil' => $item->satuanKecil->nama_satuan ?? 'Pcs',
            'gudang_id' => $item->gudang_id,
            'stok_minimal' => $item->stok_minimal ?? 0,
            'total_stok' => $totalStok,
            'stocks' => $stocks
        ];
    });
@endphp

<div class="max-w-6xl" x-data="{
    openQuickAddParty: false,
    openQuickAddFirstParty: false,
    newParty: { nama_pihak: '', instansi: '', jabatan: '', alamat: '', no_telp: '' },
    newFirstParty: { nama_pihak: '', nip: '', jabatan: '', instansi: '' },
    isSubmitting: false,
    errorMessage: '',
    successMessage: '',
    mainFormError: '',
    selectedPihakKedua: '{{ old('pihak_kedua_id', $transaction->pihak_kedua_id) ?? '' }}',
    selectedItemId: '{{ old('barang_id', $transaction->barang_id) ?? '' }}',
    selectedWarehouseId: '{{ old('gudang_id', $transaction->gudang_id) ?? '' }}',
    jumlahKecil: {{ old('jumlah_barang_kecil', $transaction->jumlah_barang_kecil) ?? 0 }},
    items: {{ json_encode($itemsData) }},
    selectedPenerimaVal: '{{ old('pihak_kesatu_id', $transaction->pihak_kesatu_id) ? 'first_party:'.$transaction->pihak_kesatu_id : (old('penerima_id', $transaction->penerima_id) ? 'user:'.$transaction->penerima_id : '') }}',
    searchQuery: '',
    showDropdown: false,

    get selectedItem() {
        return this.items.find(i => i.id == this.selectedItemId) || null;
    },
    get currentStock() {
        if (!this.selectedItem || !this.selectedWarehouseId) return { kecil: 0 };
        return this.selectedItem.stocks[this.selectedWarehouseId] || { kecil: 0 };
    },
    get isStockInsufficient() {
        if ('{{ $jenis }}' !== 'keluar') return false;
        return this.jumlahKecil > (this.selectedItem ? this.selectedItem.total_stok : 0);
    },
    init() {
        this.$watch('selectedItemId', value => {
            const item = this.items.find(i => i.id == value);
            if (item) {
                if ('{{ $jenis }}' === 'keluar') {
                    const warehouseWithStock = Object.keys(item.stocks).find(whId => item.stocks[whId].kecil > 0);
                    if (warehouseWithStock) {
                        this.selectedWarehouseId = warehouseWithStock;
                        return;
                    }
                }
                if (item.gudang_id) {
                    this.selectedWarehouseId = item.gudang_id;
                }
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
    },
    submitQuickAddFirstParty() {
        if (!this.newFirstParty.nama_pihak) return;
        this.isSubmitting = true;
        this.errorMessage = '';
        this.successMessage = '';

        const payload = {
            nama_pihak: this.newFirstParty.nama_pihak,
            nip: this.newFirstParty.nip || '',
            jabatan: this.newFirstParty.jabatan || '',
            instansi: this.newFirstParty.instansi || ''
        };

        fetch('{{ route('pihak-kesatu.store') }}', {
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
                const select = document.getElementById('penerima_select');
                const optgroup = document.getElementById('penerima_first_party_group');
                const option = document.createElement('option');
                option.value = 'first_party:' + res.data.id;
                option.text = `${res.data.nama_pihak} — ${res.data.jabatan || ''}`;
                option.selected = true;

                if (optgroup) {
                    optgroup.appendChild(option);
                } else {
                    select.add(option);
                }

                this.selectedPenerimaVal = 'first_party:' + res.data.id;

                this.newFirstParty = { nama_pihak: '', nip: '', jabatan: '', instansi: '' };
                this.openQuickAddFirstParty = false;

                this.successMessage = 'Pihak Kesatu berhasil ditambahkan: ' + res.data.nama_pihak;
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
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Edit Transaksi {{ $jenis == 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Ubah data mutasi barang {{ $jenis }} dalam sistem.</p>
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
        <ul class="mt-2 list-disc list-inside text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Main Form (Col span 2) -->
        <div class="lg:col-span-2">
            <form action="{{ route('barang-' . $jenis . '.update', $transaction->id) }}" method="POST"
                  @submit="
                      if (!selectedItemId) {
                          $event.preventDefault();
                          mainFormError = 'Barang wajib dipilih.';
                          window.scrollTo({top: 0, behavior: 'smooth'});
                      } else if ('{{ $jenis }}' === 'masuk' && !selectedPenerimaVal) {
                          $event.preventDefault();
                          mainFormError = 'Penerima wajib dipilih.';
                          window.scrollTo({top: 0, behavior: 'smooth'});
                      } else if ('{{ $jenis }}' === 'keluar' && !selectedPihakKedua) {
                          $event.preventDefault();
                          mainFormError = 'Pihak Kedua (Penerima) wajib dipilih.';
                          window.scrollTo({top: 0, behavior: 'smooth'});
                      } else if ('{{ $jenis }}' === 'keluar' && isStockInsufficient) {
                          $event.preventDefault();
                          mainFormError = 'Stok tidak mencukupi untuk melakukan transaksi keluar ini.';
                          window.scrollTo({top: 0, behavior: 'smooth'});
                      }
                  "
                  class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="jenis" value="{{ $jenis }}">

                <!-- Card 1: Barang & Lokasi -->
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-box text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Pilih Barang & Gudang</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5" x-data="{ showDropdown: false, searchQuery: '' }">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Barang <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <!-- Trigger Button -->
                                <button type="button" @click="showDropdown = !showDropdown"
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-left text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none flex justify-between items-center transition-all">
                                    <span x-text="selectedItem ? `${selectedItem.kode_barang} - ${selectedItem.nama_barang}` : 'Pilih Barang'"></span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                <input type="hidden" name="barang_id" :value="selectedItemId">

                                <!-- Dropdown Card -->
                                <div x-show="showDropdown" @click.away="showDropdown = false"
                                     class="absolute z-50 w-full mt-2 bg-white dark:bg-navy-950 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl overflow-hidden" x-cloak>
                                    <div class="p-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-navy-900/50">
                                        <div class="relative">
                                            <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-xs"></i>
                                            <input type="text" x-model="searchQuery" placeholder="Cari nama atau kode barang..."
                                                   class="w-full pl-8 pr-4 py-2 bg-white dark:bg-navy-900 border border-gray-200 dark:border-gray-700 rounded-xl text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                        </div>
                                    </div>
                                    <div class="max-h-60 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-800">
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
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">{{ $jenis == 'masuk' ? 'Gudang Tujuan' : 'Gudang Asal' }} <span class="text-red-500">*</span></label>
                            <select name="gudang_id" required x-model="selectedWarehouseId" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->nama_gudang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Tanggal & Waktu Transaksi <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="tgl_transaksi" value="{{ old('tgl_transaksi', $transaction->tgl_transaksi ? $transaction->tgl_transaksi->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Jumlah Mutasi -->
                <div class="bg-white dark:bg-navy-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 space-y-5">
                    <div class="flex items-center space-x-2 border-b border-gray-50 dark:border-gray-800 pb-3">
                        <i class="fas fa-calculator text-primary-500 text-sm"></i>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Jumlah Barang</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                                Jumlah (<span x-text="selectedItem ? selectedItem.satuan_kecil : 'Satuan'"></span>) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="jumlah_barang_kecil" x-model.number="jumlahKecil" min="1" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
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
                                    <option value="{{ $party->id }}" {{ old('pihak_kesatu_id', $transaction->pihak_kesatu_id) == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} — {{ $party->jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Pihak Kedua (Penerima)</label>
                            <div class="flex items-stretch gap-2 w-full">
                                <select name="pihak_kedua_id" id="pihak_kedua_select" x-model="selectedPihakKedua" class="flex-grow min-w-0 px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                    <option value="">Pilih Pihak Kedua</option>
                                    @foreach($secondParties as $party)
                                        <option value="{{ $party->id }}" {{ old('pihak_kedua_id', $transaction->pihak_kedua_id) == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} — {{ $party->instansi }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="openQuickAddParty = true" class="flex-shrink-0 px-3.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white font-bold transition-all shadow-md shadow-orange-500/20 flex items-center justify-center" title="Quick Add Pihak Kedua">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($jenis == 'masuk')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Nama Penerima <span class="text-red-500">*</span></label>
                            <div class="flex items-stretch gap-2 w-full">
                                <select id="penerima_select" x-model="selectedPenerimaVal" class="flex-grow min-w-0 px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all">
                                    <option value="">Pilih Penerima</option>
                                    <optgroup label="User Aktif">
                                        @foreach($users as $user)
                                            <option value="user:{{ $user->id }}" {{ (old('penerima_id', $transaction->penerima_id) == $user->id) ? 'selected' : '' }}>{{ $user->nama_lengkap }} — {{ optional($user->jabatan)->nama_jabatan }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup id="penerima_first_party_group" label="Pihak Kesatu (Master Data)">
                                        @foreach($firstParties as $party)
                                            <option value="first_party:{{ $party->id }}" {{ (old('pihak_kesatu_id', $transaction->pihak_kesatu_id) == $party->id) ? 'selected' : '' }}>{{ $party->nama_pihak }} — {{ $party->jabatan }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <button type="button" @click="openQuickAddFirstParty = true" class="flex-shrink-0 px-3.5 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white font-bold transition-all shadow-md shadow-orange-500/20 flex items-center justify-center" title="Quick Add Pihak Kesatu">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <input type="hidden" name="penerima_id" :value="selectedPenerimaVal.startsWith('user:') ? selectedPenerimaVal.split(':')[1] : ''">
                            <input type="hidden" name="pihak_kesatu_id" :value="selectedPenerimaVal.startsWith('first_party:') ? selectedPenerimaVal.split(':')[1] : ''">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Nama Pengirim <span class="text-red-500">*</span></label>
                            <input type="text" name="penerima_penyerah" required maxlength="100" value="{{ old('penerima_penyerah', $transaction->penerima_penyerah) }}"
                                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all"
                                   placeholder="Masukkan nama pengirim barang">
                        </div>
                    </div>
                    @endif

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Nomor Berita Acara (BAP)</label>
                        <input type="text" name="nomor_berita_acara" value="{{ old('nomor_berita_acara', $transaction->nomor_berita_acara) }}"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all"
                               placeholder="Contoh: 300.2.2/BA.001/Darlog/2026 (opsional)">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-navy-950 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all" placeholder="Catatan tambahan untuk transaksi ini...">{{ old('keterangan', $transaction->keterangan) }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('barang-' . $jenis . '.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-2xl transition-all text-xs border border-gray-200/50 dark:border-gray-700/50">Batal</a>
                    <button type="submit"
                            class="px-8 py-3 font-bold rounded-2xl text-white shadow-lg transition-all transform active:scale-95 text-xs flex items-center gap-2"
                            :class="isStockInsufficient ? 'bg-gray-400 cursor-not-allowed shadow-none' : '{{ $jenis == 'masuk' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/30' : 'bg-orange-600 hover:bg-orange-700 shadow-orange-500/30' }}'">
                        <i class="fas fa-save"></i> Simpan Perubahan
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
                    <!-- Barang Terpilih -->
                    <div class="p-4 bg-gray-50/50 dark:bg-navy-950/50 rounded-2xl border border-gray-100/50 dark:border-gray-800/50">
                        <div class="text-[10px] uppercase font-bold text-gray-400">Barang Terpilih</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-1" x-text="selectedItem ? selectedItem.nama_barang : ''"></div>
                        <div class="text-xs font-mono text-primary-600 mt-0.5" x-text="selectedItem ? selectedItem.kode_barang : ''"></div>
                    </div>

                    <!-- Warehouse Stock Display -->
                    <div class="space-y-3">
                        <div class="text-[10px] uppercase font-bold text-gray-400">Status Persediaan & Batas Minimal</div>

                        <!-- Total Stock Only -->
                        <div class="flex items-center justify-between p-3.5 bg-gray-50 dark:bg-navy-950 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <div>
                                <span class="text-[10px] font-medium text-gray-500 uppercase">Total Stok (Semua Gudang)</span>
                                <div class="text-base font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">
                                    <span x-text="selectedItem ? selectedItem.total_stok : 0"></span>
                                    <span class="text-xs font-normal text-gray-500" x-text="selectedItem ? selectedItem.satuan_kecil : ''"></span>
                                </div>
                            </div>
                            <i class="fas fa-cubes text-emerald-400 text-xl opacity-60"></i>
                        </div>

                        <!-- Minimum Stock Threshold -->
                        <div class="flex items-center justify-between p-3 py-2.5 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100/50 dark:border-amber-900/30 rounded-2xl text-xs">
                            <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400">
                                <i class="fas fa-circle-exclamation"></i>
                                <span class="font-semibold">Batas Stok Minimal (Warning)</span>
                            </div>
                            <div class="font-bold text-amber-800 dark:text-amber-300">
                                <span x-text="selectedItem ? selectedItem.stok_minimal : 0"></span>
                                <span x-text="selectedItem ? selectedItem.satuan_kecil : ''"></span>
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
                                <span class="text-gray-500">Sisa Stok Setelah Keluar</span>
                                <div class="font-bold" :class="(selectedItem ? selectedItem.total_stok : 0) - jumlahKecil < 0 ? 'text-red-500' : 'text-gray-800 dark:text-white'">
                                    <span x-text="selectedItem ? selectedItem.total_stok : 0"></span>
                                    <span class="mx-1">→</span>
                                    <span x-text="(selectedItem ? selectedItem.total_stok : 0) - jumlahKecil"></span>
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
                        <span>Ubah detail **Barang** atau **Gudang** jika terjadi kesalahan pencatatan awal.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="font-bold text-gray-700 dark:text-gray-300">•</span>
                        <span>Stok barang akan disesuaikan secara otomatis di latar belakang oleh sistem setelah perubahan disimpan.</span>
                    </li>
                    @if($jenis == 'keluar')
                    <li class="flex gap-2">
                        <span class="font-bold text-gray-700 dark:text-gray-300">•</span>
                        <span>Pastikan data **Pihak Kedua (Penerima)** sesuai untuk kebutuhan Berita Acara Serah Terima (BAST).</span>
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

    <!-- Modal Quick Add Pihak Kesatu -->
    <x-modal title="Tambah Pihak Kesatu (Quick Add)" x-show="openQuickAddFirstParty">
        <form @submit.prevent="submitQuickAddFirstParty" class="space-y-4">
            <template x-if="errorMessage">
                <div class="bg-red-100/80 border border-red-400 text-red-700 px-4 py-2.5 rounded-xl text-sm">
                    <span x-text="errorMessage"></span>
                </div>
            </template>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nama Pihak <span class="text-red-500">*</span></label>
                <input type="text" x-model="newFirstParty.nama_pihak" required class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" x-model="newFirstParty.nip" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                    <input type="text" x-model="newFirstParty.jabatan" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Instansi</label>
                <input type="text" x-model="newFirstParty.instansi" class="w-full px-4 py-2 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" @click="openQuickAddFirstParty = false" :disabled="isSubmitting" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                <button type="submit" :disabled="isSubmitting" class="px-6 py-2 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold rounded-xl shadow-lg shadow-orange-500/20 flex items-center gap-2">
                    <span x-show="isSubmitting" class="animate-spin text-sm"><i class="fas fa-spinner"></i></span>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                </button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
