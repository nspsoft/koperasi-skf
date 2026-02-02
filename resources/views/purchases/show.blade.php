@extends('layouts.app')

@section('title', __('messages.purchases.show_title') . ' ' . $purchase->reference_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('purchases.index') }}" class="btn-secondary-sm">
            {{ __('messages.purchases.show_back') }}
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.purchases.show_title') }}</h1>
        
        @if(auth()->user()->hasAdminAccess())
        <a href="{{ route('purchases.edit', $purchase) }}" class="ml-auto btn-primary-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit Transaksi
        </a>
        @endif
    </div>

    <!-- Status Banner -->
    <div class="glass-card p-6 border-l-4 border-{{ $purchase->status_color }}-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-sm text-gray-500 uppercase">{{ __('messages.purchases.show_status_po') }}</p>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $purchase->reference_number }}</span>
                    <span class="badge badge-{{ $purchase->status_color }} text-lg">{{ $purchase->status_label }}</span>
                </div>
                @if($purchase->status === 'completed')
                <p class="text-sm text-green-600 mt-2">✓ Selesai pada {{ $purchase->completed_at->format('d M Y H:i') }}</p>
                @endif
            </div>
            
            @if($purchase->status === 'pending')
            <div class="flex gap-2">
                <form action="{{ route('purchases.update-status', $purchase) }}" method="POST" onsubmit="return confirm('{{ __('messages.purchases.show_confirm_cancel') }}')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn-danger">{{ __('messages.purchases.show_btn_cancel') }}</button>
                </form>
                <form action="{{ route('purchases.update-status', $purchase) }}" method="POST" onsubmit="return confirm('{{ __('messages.purchases.show_confirm_receive') }}')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn-success">
                        {{ __('messages.purchases.show_btn_receive') }}
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="glass-card p-4">
            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm uppercase tracking-wide border-b pb-2">{{ __('messages.purchases.show_info_supplier') }}</h3>
            <p class="text-lg font-semibold">{{ $purchase->supplier->name }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ $purchase->supplier->address ?? '-' }}</p>
            <div class="mt-4 space-y-1 text-sm">
                <p class="flex justify-between">
                    <span class="text-gray-500">Kontak:</span>
                    <span class="font-medium">{{ $purchase->supplier->contact_person ?? '-' }}</span>
                </p>
                <p class="flex justify-between">
                    <span class="text-gray-500">Telepon:</span>
                    <span class="font-medium">{{ $purchase->supplier->phone ?? '-' }}</span>
                </p>
            </div>
        </div>

        <div class="glass-card p-4">
            <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-sm uppercase tracking-wide border-b pb-2">{{ __('messages.purchases.show_info_trx') }}</h3>
            <div class="space-y-2 text-sm">
                <p class="flex justify-between">
                    <span class="text-gray-500">{{ __('messages.purchases.show_date') }}:</span>
                    <span class="font-medium">{{ $purchase->purchase_date->format('d F Y') }}</span>
                </p>
                <p class="flex justify-between">
                    <span class="text-gray-500">{{ __('messages.purchases.show_created_by') }}:</span>
                    <span class="font-medium">{{ $purchase->creator->name ?? 'System' }}</span>
                </p>
                @if($purchase->note)
                <div class="mt-2 pt-2 border-t border-dashed">
                    <p class="text-gray-500 mb-1">{{ __('messages.purchases.show_notes') }}:</p>
                    <p class="text-gray-800 dark:text-gray-300 italic">"{{ $purchase->note }}"</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Items -->
    <div class="glass-card overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700/50 p-3 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">{{ __('messages.purchases.show_items_title') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>{{ __('messages.purchases.show_table_product') }}</th>
                        <th class="text-center">{{ __('messages.purchases.show_table_qty') }}</th>
                        <th class="text-right">{{ __('messages.purchases.show_table_cost') }}</th>
                        <th class="text-right">{{ __('messages.purchases.show_table_subtotal') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($item->product->image)
                                <img src="{{ \Storage::url($item->product->image) }}" class="w-10 h-10 rounded object-cover">
                                @else
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-xs">No Img</div>
                                @endif
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->product->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right text-gray-600">Rp {{ number_format($item->cost, 0, ',', '.') }}</td>
                        <td class="text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="3" class="text-right font-bold py-4 px-6">{{ __('messages.purchases.show_total_purchase') }}</td>
                        <td class="text-right font-bold py-4 px-6 text-xl text-primary-600">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
