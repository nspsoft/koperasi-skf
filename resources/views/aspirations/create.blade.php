@extends('layouts.app')

@section('title', 'Aspirasi Anggota')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Suara Anggota</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bantu kami membangun Koperasi SKF yang lebih baik.</p>
    </div>
    <a href="{{ route('aspirations.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Riwayat Aspirasi
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Form Section 1: Permintaan Barang --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transform transition hover:shadow-xl duration-300">
        <div class="p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Request Barang Baru</h2>
                    <p class="text-sm text-gray-500">Usulkan barang yang ingin Anda beli di Koperasi Mart.</p>
                </div>
            </div>

            <form action="{{ route('aspirations.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="item_request">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Nama Barang</label>
                    <input type="text" name="data[item_name]" required placeholder="Contoh: Susu UHT Ultra 1L" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition duration-200" list="productList">
                    <datalist id="productList">
                        @foreach($products as $product)
                            <option value="{{ $product->name }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
                    <select name="data[category]" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition duration-200">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Biasanya Beli Berapa Banyak?</label>
                        <div class="relative">
                            <input type="number" name="data[qty]" required placeholder="1" min="1" class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition duration-200">
                            <span class="absolute right-4 top-3 text-gray-400 text-sm font-medium">Pcs/Pak</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Estimasi Harga Wajar (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="data[estimated_price]" placeholder="Contoh: 15000" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 transition duration-200">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Seberapa Sering Anda Membeli Ini?</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[frequency]" value="Harian" class="peer hidden" required>
                            <div class="px-4 py-3 text-center rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition duration-200 font-medium group-hover:border-emerald-500">Harian</div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[frequency]" value="Mingguan" class="peer hidden">
                            <div class="px-4 py-3 text-center rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition duration-200 font-medium group-hover:border-emerald-500">Mingguan</div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[frequency]" value="Bulanan" class="peer hidden">
                            <div class="px-4 py-3 text-center rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 transition duration-200 font-medium group-hover:border-emerald-500">Bulanan</div>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full mt-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-emerald-500/30 transform transition hover:-translate-y-1 active:scale-95 duration-200">
                    Kirim Usulan Barang
                </button>
            </form>
        </div>
    </div>

    {{-- Form Section 2: Evaluasi Sistem --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transform transition hover:shadow-xl duration-300">
        <div class="p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Evaluasi Koperasi</h2>
                    <p class="text-sm text-gray-500">Bantu kami meningkatkan kualitas layanan sistem.</p>
                </div>
            </div>

            <form action="{{ route('aspirations.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="type" value="system_eval">

                {{-- Rating Sistem (1-5 Stars) --}}
                <div x-data="{ rating: 0 }">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rating Sistem Koperasi</label>
                    <input type="hidden" name="data[rating]" :value="rating" required>
                    <div class="flex gap-2">
                        <template x-for="star in 5" :key="star">
                            <button type="button" @click="rating = star" class="focus:outline-none transition-transform hover:scale-110">
                                <svg class="w-10 h-10 transition-colors" :class="star <= rating ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </template>
                        <span class="ml-2 text-sm font-medium text-gray-500 self-center" x-show="rating > 0" x-text="rating + ' / 5'"></span>
                    </div>
                </div>

                {{-- Kepuasan Pelayanan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Kepuasan Pelayanan</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[satisfaction]" value="puas" class="peer hidden" required>
                            <div class="px-4 py-3 text-center rounded-xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all duration-200">
                                <div class="text-2xl mb-1">😊</div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">Puas</div>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[satisfaction]" value="netral" class="peer hidden">
                            <div class="px-4 py-3 text-center rounded-xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 transition-all duration-200">
                                <div class="text-2xl mb-1">😐</div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">Netral</div>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[satisfaction]" value="tidak_puas" class="peer hidden">
                            <div class="px-4 py-3 text-center rounded-xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 transition-all duration-200">
                                <div class="text-2xl mb-1">😞</div>
                                <div class="text-xs font-bold text-gray-700 dark:text-gray-300">Tidak Puas</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pilih Sistem Koperasi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih Sistem Koperasi</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[system_choice]" value="manual" class="peer hidden" required>
                            <div class="px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all duration-200 h-full">
                                <div class="font-bold text-gray-900 dark:text-white mb-1">Manual</div>
                                <div class="text-[10px] text-gray-500 leading-tight">Proses fisik, buku tabungan manual, tatap muka.</div>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[system_choice]" value="digital" class="peer hidden">
                            <div class="px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all duration-200 h-full">
                                <div class="font-bold text-gray-900 dark:text-white mb-1">Digital (Mobile App)</div>
                                <div class="text-[10px] text-gray-500 leading-tight">Proses cepat, pantau saldo kapan saja, transaksi online.</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pilih Sistem Pembayaran --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pilih Sistem Pembayaran di Toko</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[payment_choice]" value="cash" class="peer hidden" required>
                            <div class="px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all duration-200 h-full">
                                <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    Tunai (Cash)
                                </div>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="data[payment_choice]" value="digital" class="peer hidden">
                            <div class="px-4 py-4 rounded-2xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 transition-all duration-200 h-full">
                                <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Digital (QRIS/E-Wallet)
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Saran/Masukan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Saran & Masukan</label>
                    <textarea name="data[suggestion]" rows="3" placeholder="Tuliskan saran, masukan, atau kritik Anda untuk kemajuan Koperasi SKF..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 transition duration-200"></textarea>
                </div>

                <button type="submit" class="w-full mt-2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-amber-500/30 transform transition hover:-translate-y-1 active:scale-95 duration-200">
                    Kirim Evaluasi Sistem
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productData = @json($products);
        const nameInput = document.querySelector('input[name="data[item_name]"]');
        const catSelect = document.querySelector('select[name="data[category]"]');

        if(nameInput && catSelect) {
            nameInput.addEventListener('input', function() {
                const val = this.value;
                const match = productData.find(p => p.name === val);
                
                if (match && match.category_name) {
                    // Try to match exact value first
                    let options = Array.from(catSelect.options);
                    let optionToSelect = options.find(opt => opt.value === match.category_name);
                    
                    if(optionToSelect) {
                        catSelect.value = match.category_name;
                        // Visual cues (optional blink effect)
                        catSelect.classList.add('ring-2', 'ring-emerald-500');
                        setTimeout(() => catSelect.classList.remove('ring-2', 'ring-emerald-500'), 500);
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
