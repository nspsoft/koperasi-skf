<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kartu Anggota Digital') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <!-- Digital Card -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl shadow-xl overflow-hidden text-white relative">
                <!-- Decorative Circles -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 rounded-full bg-white opacity-5"></div>

                <div class="p-6 relative z-10">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/spindo-logo.png') }}" alt="Logo" class="h-10 w-auto bg-white rounded p-1">
                            <div>
                                <h3 class="font-bold text-lg leading-none">KOPERASI SKF</h3>
                                <p class="text-xs text-blue-100 opacity-80">Kartu Anggota Digital</p>
                            </div>
                        </div>
                        <div class="text-right">
                             <div class="text-xs text-blue-200">Status</div>
                             <div class="font-bold text-sm bg-green-500 px-2 py-0.5 rounded-full text-white inline-block">
                                {{ ucfirst($member->status ?? 'Active') }}
                             </div>
                        </div>
                    </div>

                    <!-- Member Info -->
                    <div class="flex items-start space-x-4 mb-6">
                        <div class="flex-shrink-0">
                            @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" alt="Photo" class="w-24 h-24 rounded-lg object-cover border-2 border-white/30 shadow-sm">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-white/20 flex items-center justify-center border-2 border-white/30 text-2xl font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xl font-bold truncate mb-1">{{ $user->name }}</h4>
                            <div class="space-y-1 text-sm text-blue-50">
                                <div class="flex">
                                    <span class="w-20 opacity-70">ID Anggota</span>
                                    <span class="font-mono">: {{ $member->member_id }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 opacity-70">NIK</span>
                                    <span class="font-mono">: {{ $member->employee_id ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-20 opacity-70">Jabatan</span>
                                    <span class="truncate">: {{ $member->position ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="bg-white rounded-xl p-4 flex items-center justify-between shadow-lg">
                        <div class="text-gray-800">
                            <p class="text-xs text-gray-500 mb-1">Scan QR Code ini untuk transaksi</p>
                            <p class="font-mono font-bold text-lg tracking-wider text-blue-600">{{ $member->id_amigo ?? $member->member_id }}</p>
                        </div>
                        <div class="bg-white p-1 rounded">
                             {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($member->id_amigo ?? $member->member_id) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-center space-x-4">
                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </a>
                <button onclick="window.print()" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Kartu
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
