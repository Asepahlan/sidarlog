@extends('layouts.app')

@section('title', 'Tambah Transaksi ' . ucfirst($jenis))

@section('content')
<div class="max-w-4xl" x-data="{ 
    openQuickAddParty: false,
    newParty: { nama_pihak: '', instansi: '', jabatan: '', alamat: '', no_telp: '' },
    isSubmitting: false,
    errorMessage: '',
    successMessage: '',
    mainFormError: '',
    selectedPihakKedua: '{{ old('pihak_kedua_id') ?? '' }}',
    penerimaLainnya: '{{ old('penerima_penyerah') ?? '' }}',
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
        
        // Map form fields to DB columns
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
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Tambah Transaksi {{ ucfirst($jenis) }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Input data mutasi barang {{ $jenis }} ke sistem.</p>
    </div>

    <!-- Client-side Error Alert -->
    <div x-show="mainFormError" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative flex items-start gap-3" role="alert" x-cloak>
        <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
        <div>
            <strong class="font-bold block">Gagal menyimpan!</strong>
            <span class="text-sm mt-0.5 block" x-text="mainFormError"></span>
        </div>
    </div>

    <!-- AJAX Success Alert -->
    <div x-show="successMessage" class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-300" role="alert" x-cloak>
        <i class="fas fa-check-circle text-lg"></i>
        <span class="block sm:inline font-medium" x-text="successMessage"></span>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
        <strong class="font-bold">Gagal menyimpan!</strong>
        <ul class="mt-1 list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 max-w-full">
        <form action="{{ route('barang-' . $jenis . '.store') }}" method="POST" @submit="if('{{ $jenis }}' === 'keluar' && !selectedPihakKedua && !penerimaLainnya) { $event.preventDefault(); mainFormError = 'Penerima wajib ditentukan (Pilih Pihak Kedua atau isi Nama Penerima Lainnya).'; window.scrollTo({top: 0, behavior: 'smooth'}); }" class="space-y-6">
            @csrf
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Barang</label>
                    <select name="barang_id" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Barang</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('barang_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->kode_barang }} - {{ $item->nama_barang }} 
                                (Stok: {{ $item->current_stock_kecil }} {{ $item->satuanKecil->nama_satuan ?? 'Unit' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Gudang</label>
                    <select name="gudang_id" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('gudang_id') == $wh->id ? 'selected' : '' }}>{{ $wh->nama_gudang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah (Kecil)</label>
                    <input type="number" name="jumlah_barang_kecil" value="{{ old('jumlah_barang_kecil', 0) }}" min="0" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Jumlah (Besar)</label>
                    <input type="number" name="jumlah_barang_besar" value="{{ old('jumlah_barang_besar', 0) }}" min="0" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tanggal Transaksi</label>
                    <input type="datetime-local" name="tgl_transaksi" value="{{ old('tgl_transaksi', now()->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>

            @if($jenis == 'keluar')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pihak Kesatu (BPBD)</label>
                    <select name="pihak_kesatu_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Pilih Pihak Pertama</option>
                        @foreach($firstParties as $party)
                            <option value="{{ $party->id }}" {{ old('pihak_kesatu_id') == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} - {{ $party->jabatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pihak Kedua (Penerima)</label>
                    <div class="flex items-stretch gap-2 w-full">
                        <select name="pihak_kedua_id" id="pihak_kedua_select" x-model="selectedPihakKedua" class="flex-grow min-w-0 px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                            <option value="">Pilih Pihak Kedua</option>
                            @foreach($secondParties as $party)
                                <option value="{{ $party->id }}" {{ old('pihak_kedua_id') == $party->id ? 'selected' : '' }}>{{ $party->nama_pihak }} - {{ $party->instansi }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="openQuickAddParty = true" class="flex-shrink-0 px-4 rounded-xl bg-[#f97316] hover:bg-[#ea580c] text-white font-bold transition-all shadow-md shadow-orange-500/20 flex items-center justify-center" title="Quick Add Pihak Kedua">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor Berita Acara (BAP)</label>
                    <select name="reference_bap_id" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">— Tanpa BAP —</option>
                        @foreach($baps as $bap)
                            <option value="{{ $bap->id }}" {{ old('reference_bap_id') == $bap->id ? 'selected' : '' }}>
                                {{ $bap->nomor_ba }} — {{ $bap->judul_ba }} ({{ \Carbon\Carbon::parse($bap->tgl_ba)->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ $jenis == 'masuk' ? 'Nama Pengirim' : 'Nama Penerima (Lainnya)' }}</label>
                <input type="text" name="penerima_penyerah" x-model="penerimaLainnya" :disabled="selectedPihakKedua !== ''" class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500 transition-all" :class="selectedPihakKedua !== '' ? 'bg-gray-100 dark:bg-gray-700/50 cursor-not-allowed text-gray-400 border-gray-200 dark:border-gray-700' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 dark:text-white'" :placeholder="selectedPihakKedua !== '' ? 'Dinonaktifkan karena penerima dipilih dari daftar.' : 'Masukkan nama personil/pihak terkait'">
                @if($jenis == 'keluar')
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        * Gunakan kolom ini hanya jika penerima belum tersedia pada daftar Pihak Kedua.
                    </p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-3 border rounded-xl dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Catatan tambahan untuk transaksi ini...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-3">
                <a href="{{ route('barang-' . $jenis . '.index') }}" class="px-6 py-3 text-gray-500 hover:text-gray-700 font-bold transition-colors">Batal</a>
                <button type="submit" class="px-8 py-3 bg-[#f97316] hover:bg-[#ea580c] text-white font-bold rounded-2xl shadow-lg shadow-orange-500/30 transition-all transform active:scale-95">
                    <i class="fas fa-save mr-2"></i> Simpan Transaksi
                </button>
            </div>
        </form>
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
