@extends('layouts.app')

@section('title', 'Manajemen Template')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Template Dokumen</h1>
            <p class="page-subtitle">Atur kode surat default agar tidak perlu mengetik ulang.</p>
        </div>
    </div>

    <div class="glass-card-solid overflow-hidden">
        <div class="table-scroll-container">
            <table class="table-modern">
                <thead class="bg-primary-600 dark:bg-primary-700 text-white">
                    <tr>
                        <th class="w-10">No</th>
                        <th>Nama Dokumen</th>
                        <th class="w-48 text-center">Kode Default</th>
                        <th class="w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $index => $template)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="font-medium text-gray-900 dark:text-white">{{ $template->name }}</td>
                        <td class="text-center">
                            @if($template->code)
                                <span class="badge badge-primary">{{ $template->code }}</span>
                            @else
                                <span class="badge badge-warning">Belum Ada</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('document-templates.edit', $template->id) }}" 
                               class="btn-sm btn-secondary">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
