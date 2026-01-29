@extends('layouts.app')

@section('title', __('messages.journals_page.manual_title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('messages.journals_page.manual_title') }}</h1>
        <p class="page-subtitle">{{ __('messages.journals_page.manual_subtitle') }}</p>
    </div>
    <div>
        <a href="{{ route('journals.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('messages.back') }}
        </a>
    </div>
</div>

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('journals.store') }}" method="POST" x-data="journalForm()">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Header Data --}}
        <div class="lg:col-span-3">
            <div class="glass-card-solid p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">{{ __('messages.journals_page.transaction_date') }}</label>
                        <input type="date" name="transaction_date" class="form-input" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    </div>
                    <div>
                        <label class="form-label">{{ __('messages.journals_page.table_desc') }}</label>
                        <input type="text" name="description" class="form-input" value="{{ old('description') }}" placeholder="{{ __('messages.journals_page.desc_placeholder') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction Lines --}}
        <div class="lg:col-span-3">
            <div class="glass-card-solid overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-bold">{{ __('messages.journals_page.journal_items') }}</h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 dark:bg-gray-700/30">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.account') }}</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">{{ __('messages.journals_page.table_desc') }}</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right w-44">{{ __('messages.journals_page.debit') }}</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right w-44">{{ __('messages.journals_page.credit') }}</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-center w-16"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(line, index) in lines" :key="index">
                                <tr class="border-t border-gray-100 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-700/20">
                                    <td class="px-6 py-3">
                                        <select :name="'lines['+index+'][account_code]'" x-model="line.account_code" class="form-input text-sm" required>
                                            <option value="">{{ __('messages.journals_page.select_account') }}</option>
                                            @foreach($accounts as $type => $group)
                                                <optgroup label="{{ strtoupper(__('messages.journals_page.account_types.' . $type)) }}">
                                                    @foreach($group as $account)
                                                        <option value="{{ $account->code }}">{{ $account->code }} - {{ $account->name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="text" :name="'lines['+index+'][description]'" x-model="line.description" class="form-input text-sm" placeholder="Sama seperti ket. umum">
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs text-right w-4">Rp</span>
                                            <input type="number" :name="'lines['+index+'][debit]'" x-model.number="line.debit" @input="calculateTotals()" class="form-input text-sm text-right pl-8" min="0" step="0.01">
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs text-right w-4">Rp</span>
                                            <input type="number" :name="'lines['+index+'][credit]'" x-model.number="line.credit" @input="calculateTotals()" class="form-input text-sm text-right pl-8" min="0" step="0.01">
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <button type="button" @click="removeLine(index)" class="text-red-500 hover:text-red-700" x-show="lines.length > 2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50/50 dark:bg-gray-800/50 font-bold border-t-2 border-gray-200 dark:border-gray-700">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right">TOTAL</td>
                                <td class="px-6 py-4 text-right text-primary-600" x-text="'Rp ' + formatNumber(totalDebit)"></td>
                                <td class="px-6 py-4 text-right text-primary-600" x-text="'Rp ' + formatNumber(totalCredit)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/30">
                    <button type="button" @click="addLine()" class="btn-secondary text-sm py-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('messages.journals_page.add_row') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer/Action --}}
        <div class="lg:col-span-3">
            <div class="glass-card-solid p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 rounded-full" :class="isBalanced ? 'bg-green-500' : 'bg-red-500'"></div>
                    <span class="text-sm font-medium" :class="isBalanced ? 'text-green-600' : 'text-red-600'" 
                          x-text="isBalanced ? '{{ __('messages.journals_page.balanced_msg') }}' : '{{ __('messages.journals_page.unbalanced_msg') }}: Rp ' + formatNumber(Math.abs(totalDebit - totalCredit))">
                    </span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('journals.index') }}" class="btn-secondary">{{ __('messages.cancel') }}</a>
                    <button type="submit" class="btn-primary" :disabled="!isBalanced || totalDebit === 0">
                        {{ __('messages.journals_page.save_journal') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function journalForm() {
    return {
        lines: [
            { account_code: '', description: '', debit: 0, credit: 0 },
            { account_code: '', description: '', debit: 0, credit: 0 }
        ],
        totalDebit: 0,
        totalCredit: 0,
        isBalanced: true,

        calculateTotals() {
            this.totalDebit = this.lines.reduce((sum, line) => sum + (parseFloat(line.debit) || 0), 0);
            this.totalCredit = this.lines.reduce((sum, line) => sum + (parseFloat(line.credit) || 0), 0);
            this.isBalanced = Math.abs(this.totalDebit - this.totalCredit) < 0.01;
        },

        addLine() {
            this.lines.push({ account_code: '', description: '', debit: 0, credit: 0 });
        },

        removeLine(index) {
            this.lines.splice(index, 1);
            this.calculateTotals();
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }
}
</script>
@endpush
