@extends('layouts.app')

@section('title', 'Buat Dokumen - ' . $template->name)

@section('content')
    <div class="page-header">
        <div class="flex items-center gap-4">
            <a href="{{ route('documents.index') }}" class="btn-secondary p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $template->name }}</h1>
                <p class="page-subtitle">Lengkapi data untuk generate dokumen</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="glass-card-solid p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-200 dark:border-gray-700 pb-2">Informasi Dokumen</h3>
            
            <form action="{{ route('documents.generate', $template->id) }}" method="POST" target="_blank">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @foreach($placeholders as $placeholder)
                        <div>
                            <label class="form-label">{{ ucwords(str_replace('_', ' ', $placeholder)) }}</label>
                            @if(in_array($placeholder, ['isi_pemberitahuan']))
                                {{-- Rich text editor handled separately --}}
                            @elseif(in_array($placeholder, ['isi_pernyataan', 'alasan', 'agenda', 'keperluan', 'susunan_pengurus_lainnya', 'susunan_pengawas_lainnya']))
                                <textarea name="{{ $placeholder }}" class="form-input" rows="4" required placeholder="Masukkan {{ str_replace('_', ' ', $placeholder) }}...">{{ old($placeholder, $defaults[$placeholder] ?? '') }}</textarea>
                            @elseif($placeholder === 'periode')
                                <input type="month" 
                                       name="{{ $placeholder }}" 
                                       class="form-input" 
                                       value="{{ old($placeholder, $defaults[$placeholder] ?? '') }}"
                                       required>
                            @elseif(in_array($placeholder, ['nama_anggota', 'nama_user']))
                                <select name="{{ $placeholder }}" id="select_nama_anggota" class="form-input" required>
                                    <option value="">-- Pilih Anggota --</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->user->name }}" 
                                                data-nik="{{ $member->employee_id }}"
                                                data-no-anggota="{{ $member->member_id }}"
                                                data-posisi="{{ $member->position ?? '-' }}"
                                                {{ old($placeholder, $defaults[$placeholder] ?? '') == $member->user->name ? 'selected' : '' }}>
                                            {{ $member->user->name }} - {{ $member->employee_id }}
                                        </option>
                                    @endforeach
                                    <!-- Fallback for non-member users or manual entry if needed, though usually strict selection is better -->
                                    @if(isset($defaults[$placeholder]) && !$members->contains('user.name', $defaults[$placeholder]))
                                         <option value="{{ $defaults[$placeholder] }}" selected>{{ $defaults[$placeholder] }} (Saat Ini)</option>
                                    @endif
                                </select>
                            @elseif(in_array($placeholder, ['nomor_surat', 'no_surat', 'nomor']))
                                <div class="relative">
                                    <input type="text" 
                                           name="{{ $placeholder }}" 
                                           id="input_{{ $placeholder }}"
                                           class="form-input pr-24 font-bold text-primary-700 dark:text-primary-300" 
                                           value="{{ old($placeholder, $defaults[$placeholder] ?? '') }}"
                                           required>
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded border border-amber-200 dark:border-amber-800/50">
                                        Bisa Diedit
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    Nomor otomatis dihasilkan oleh sistem
                                </p>
                            @else
                                <input type="{{ str_contains($placeholder, 'tanggal') ? 'date' : (str_contains($placeholder, 'jumlah') ? 'number' : 'text') }}" 
                                       name="{{ $placeholder }}" 
                                       id="input_{{ $placeholder }}"
                                       class="form-input" 
                                       value="{{ old($placeholder, $defaults[$placeholder] ?? '') }}"
                                       required 
                                       placeholder="Masukkan {{ str_replace('_', ' ', $placeholder) }}...">
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Full-width Rich Text Editor for Isi Pemberitahuan --}}
                @if(in_array('isi_pemberitahuan', $placeholders))
                <div class="mb-8">
                    <label class="form-label text-lg font-semibold">Isi Pemberitahuan</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Gunakan toolbar untuk format: bold, italic, nomor urut, dan indentasi.</p>
                    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
                    <div id="editor-container" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600" style="min-height: 300px;"></div>
                    <textarea name="isi_pemberitahuan" id="isi_pemberitahuan_hidden" class="hidden" required>{{ old('isi_pemberitahuan', $defaults['isi_pemberitahuan'] ?? '') }}</textarea>
                </div>
                @endif

                <div class="mt-8 flex items-center justify-end gap-3">
                    <a href="{{ route('documents.index') }}" class="btn-secondary">Batal</a>
                    
                    @if($template->name === 'Surat Pemotongan Payroll Kredit Mart')
                    <button type="button" id="btnNotifyWA" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Kirim Notifikasi WA
                    </button>
                    @endif

                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Generate & Print PDF
                    </button>
                </div>
            </form>
        </div>

        @push('scripts')
        <script>
            document.getElementById('btnNotifyWA')?.addEventListener('click', async function() {
                const periode = document.querySelector('[name="periode"]')?.value;
                if (!periode) {
                    alert('Silakan pilih periode terlebih dahulu.');
                    return;
                }

                if (!confirm('Kirim notifikasi WA massal ke semua anggota yang memiliki tagihan di periode ini?')) {
                    return;
                }

                const btn = this;
                const originalContent = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24">...</svg> Mengirim...';

                try {
                    const response = await fetch('{{ route("documents.notify-wa", $template->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ periode: periode })
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        alert(result.message);
                    } else {
                        alert('Gagal: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengirim notifikasi.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
            });

            // Auto-fill NIK, No Anggota, and Posisi/Jabatan when Name is selected
            const selectMember = document.getElementById('select_nama_anggota');
            const inputNik = document.getElementById('input_nik');
            const inputNoAnggota = document.getElementById('input_no_anggota');
            const inputPosisi = document.getElementById('input_posisi'); // For surat penunjukan
            const inputJabatan = document.getElementById('input_jabatan'); // For surat keterangan anggota

            if (selectMember) {
                selectMember.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const nik = selectedOption.getAttribute('data-nik');
                    const noAnggota = selectedOption.getAttribute('data-no-anggota');
                    const posisi = selectedOption.getAttribute('data-posisi');

                    if (inputNik && nik) {
                        inputNik.value = nik;
                    }
                    if (inputNoAnggota && noAnggota) {
                        inputNoAnggota.value = noAnggota;
                    }
                    // Auto-fill Posisi OR Jabatan depending on which exists
                    if (posisi) {
                        if (inputPosisi) inputPosisi.value = posisi;
                        if (inputJabatan) inputJabatan.value = posisi;
                    }
                });
            }
        </script>

        {{-- Quill Rich Text Editor --}}
        @if(in_array('isi_pemberitahuan', $placeholders ?? []))
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            var quill = new Quill('#editor-container', {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        ['clean']
                    ]
                },
                placeholder: 'Ketik isi pemberitahuan di sini...\n\nContoh:\n1. Memberikan sarana pembelajaran praktik...\n2. Membantu operasional toko...',
                theme: 'snow'
            });

            // Load existing content
            var existingContent = document.getElementById('isi_pemberitahuan_hidden').value;
            if (existingContent) {
                quill.root.innerHTML = existingContent;
            }

            // Sync Quill content to hidden textarea before form submit
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('isi_pemberitahuan_hidden').value = quill.root.innerHTML;
            });
        </script>
        @endif
        @endpush

        <div class="mt-6 glass-card-solid p-4 bg-primary-50 dark:bg-primary-900/10 border-primary-100 dark:border-primary-800">
            <div class="flex gap-3">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-sm text-primary-700 dark:text-primary-300">
                    <p class="font-bold mb-1">Tips:</p>
                    <p>Beberapa data seperti tanggal surat, bulan, dan tahun akan diisi secara otomatis oleh sistem sesuai waktu saat ini.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
