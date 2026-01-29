@extends('layouts.app')

@section('title', 'Hasil Pemilihan - ' . $poll->title)

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Hasil Pemilihan</h1>
            <p class="page-subtitle">{{ $poll->title }}</p>
        </div>
        <a href="{{ route('polls.index') }}" class="btn-secondary">Kembali</a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="glass-card-solid p-8">
            <div class="flex items-center justify-between mb-8 border-b pb-6 border-gray-100 dark:border-gray-700">
                <div class="text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Total Suara Masuk</h2>
                    <div class="flex items-center justify-center md:justify-start gap-3">
                        <span class="text-4xl font-extrabold text-primary-600">{{ number_format($totalVotes) }}</span>
                        <span class="text-gray-500 text-sm">Suara Sah</span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <span class="text-xs font-bold px-4 py-2 rounded-full uppercase {{ $poll->status === 'active' ? 'bg-green-500 text-white' : ($poll->status === 'closed' ? 'bg-red-500 text-white' : 'bg-gray-500 text-white') }}">
                        {{ $poll->status }}
                    </span>
                </div>
            </div>

            <div class="space-y-8">
                @foreach($poll->options as $index => $option)
                    @php
                        $percentage = $totalVotes > 0 ? ($option->votes_count / $totalVotes) * 100 : 0;
                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-amber-500', 'bg-red-500', 'bg-indigo-500'];
                        $colorClass = $colors[$index % count($colors)];
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 flex-shrink-0 border-2 border-white dark:border-gray-700 shadow-sm">
                                    @if($option->candidate_photo)
                                        <img src="{{ Storage::url($option->candidate_photo) }}" alt="{{ $option->candidate_name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-full h-full p-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $option->candidate_name }}</h4>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($option->votes_count) }} Suara</span>
                                <span class="text-xs text-gray-500 block">{{ number_format($percentage, 1) }}%</span>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full h-4 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                            <div class="h-full {{ $colorClass }} transition-all duration-1000 ease-out rounded-full shadow-lg" 
                                 style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-gray-100 dark:border-gray-700">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Informasi Tambahan</h4>
                <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-2 prose dark:prose-invert max-w-none">
                    <li>Hasil pemilihan bersifat <strong>Real-Time</strong> dan otomatis diperbarui setiap kali suara baru masuk.</li>
                    <li>Sistem menjamin kerahasiaan pilihan anggota (voting anonim).</li>
                    <li>Waktu penutupan resmi: <strong>{{ $poll->end_date->format('d M Y, H:i') }}</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
