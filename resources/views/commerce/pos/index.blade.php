@extends('layouts.app')

@section('title', __('messages.pos.title'))

@section('content')
<div class="w-full flex gap-6 h-[calc(100vh-7rem)]" x-data="posSystem()">
    <!-- Flying Image Container -->
    <div id="flying-container" class="fixed inset-0 pointer-events-none z-[100]"></div>
    
    <!-- Left: Products Section -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Header with Search & Category Filter -->
        <div class="glass-card-solid p-4 mb-4">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-[3] relative">
                    <input type="text" x-model="search" @input="currentPage = 1" 
                           @keydown.enter.prevent="handleBarcodeScan()"
                           placeholder="{{ __('messages.pos.search_placeholder') }}" 
                           class="w-full form-input pl-4 pr-10 py-3 text-base" autofocus>
                    <button x-show="search" @click="search = ''; currentPage = 1" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>
                <button @click="startScanner()" class="bg-gray-800 text-white p-3 rounded-lg hover:bg-gray-700 transition-colors" title="{{ __('messages.pos.scan_camera') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </button>
                <select x-model="selectedCategory" @change="currentPage = 1" class="form-input py-3 flex-1 md:max-w-[180px]">
                    <option value="">{{ __('messages.pos.all_categories') }}</option>
                    @foreach(\App\Models\Category::all() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Products Grid with Pagination -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <div class="flex-1 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <template x-for="product in paginatedProducts" :key="product.id">
                        <div @click="addToCart(product, $event)" 
                             class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-3 cursor-pointer hover:border-primary-500 hover:shadow-lg hover:shadow-primary-500/10 transition-all duration-200 flex flex-col group"
                             :class="{'opacity-50 cursor-not-allowed': product.stock <= 0}">
                            <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-lg mb-2 overflow-hidden flex items-center justify-center relative">
                                <!-- Image handling with error fallback -->
                                <img x-show="product.image && !product.image_error" 
                                     :src="product.image ? '/storage/' + product.image : ''" 
                                     x-on:error="product.image_error = true"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                <div x-show="!product.image || product.image_error"
                                     class="w-full h-full flex items-center justify-center text-2xl font-bold absolute inset-0"
                                     :class="getPlaceholderColor(product.id)">
                                    <span x-text="getInitials(product.name)"></span>
                                </div>
                                <div x-show="product.stock <= 5" class="absolute top-1 right-1 px-1.5 py-0.5 text-[10px] font-bold rounded"
                                     :class="product.stock <= 0 ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white'"
                                     x-text="product.stock <= 0 ? '{{ __('messages.shop_page.out_of_stock') }}' : '{{ __('messages.shop_page.left') }} ' + product.stock"></div>
                                
                                <!-- Quick Edit Image Button -->
                                <button @click.stop="triggerImageUpload(product.id)" 
                                        class="absolute bottom-1 right-1 p-1.5 rounded-full bg-white/90 text-gray-700 hover:text-primary-600 hover:bg-white shadow-sm opacity-0 group-hover:opacity-100 transition-all transform scale-90 group-hover:scale-100"
                                        title="{{ __('messages.pos.change_photo') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </button>
                            </div>
                            <div class="flex-1 min-h-0">
                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm leading-tight line-clamp-2 mb-0.5" x-text="product.name"></h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="product.code"></p>
                            </div>
                            <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <span class="font-bold text-primary-600 text-sm" x-text="formatRupiah(product.price)"></span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700" 
                                      :class="product.stock > 10 ? 'text-green-600' : product.stock > 0 ? 'text-yellow-600' : 'text-red-600'"
                                      x-text="product.stock + ' {{ __('messages.pos.stock_unit') }}'"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="filteredProducts.length === 0" class="text-center py-12 text-gray-600 dark:text-gray-400">
                    <span class="text-4xl block mb-2">🔍</span>
                    {{ __('messages.pos.not_found') }}
                </div>
            </div>

            <!-- Pagination -->
            <div x-show="totalPages > 1" class="flex items-center justify-between py-3 px-1 border-t border-gray-100 dark:border-gray-700 mt-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('messages.pos.page') }} <span x-text="currentPage"></span> {{ __('messages.pos.of') }} <span x-text="totalPages"></span>
                    (<span x-text="filteredProducts.length"></span> {{ __('messages.pos.products_unit') }})
                </span>
                <div class="flex gap-1">
                    <button @click="currentPage = 1" :disabled="currentPage === 1" 
                            class="px-2 py-1 rounded text-sm text-gray-700 dark:text-gray-300 disabled:opacity-30 hover:bg-gray-100 dark:hover:bg-gray-700">⏮</button>
                    <button @click="currentPage--" :disabled="currentPage === 1" 
                            class="px-3 py-1 rounded text-sm text-gray-700 dark:text-gray-300 disabled:opacity-30 hover:bg-gray-100 dark:hover:bg-gray-700">◀ Prev</button>
                    <button @click="currentPage++" :disabled="currentPage === totalPages" 
                            class="px-3 py-1 rounded text-sm text-gray-700 dark:text-gray-300 disabled:opacity-30 hover:bg-gray-100 dark:hover:bg-gray-700">Next ▶</button>
                    <button @click="currentPage = totalPages" :disabled="currentPage === totalPages" 
                            class="px-2 py-1 rounded text-sm text-gray-700 dark:text-gray-300 disabled:opacity-30 hover:bg-gray-100 dark:hover:bg-gray-700">⏭</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Cart Section -->
    <div style="width: 450px; min-width: 450px; max-width: 450px;" class="flex-shrink-0 flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden relative">
        <!-- Cart Target for Animation -->
        <div id="cart-target" class="absolute top-4 left-4 w-10 h-10 pointer-events-none z-10"></div>
        
        <!-- Cart Header -->
        <div class="p-4 bg-primary-600 text-white">
            <h2 class="text-lg font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                {{ __('messages.pos.cart_title') }}
            </h2>
            <p class="text-white/80 text-sm" x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })"></p>
        </div>

        <!-- Buyer Type Selection -->
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2 block">{{ __('messages.pos.buyer_type') }}</label>
            <div class="flex gap-2 mb-2">
                <button @click="buyerType = 'umum'; memberId = ''; memberData = null" 
                        :class="buyerType === 'umum' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm transition-colors">
                    👤 {{ __('messages.pos.general') }}
                </button>
                <button @click="buyerType = 'member'" 
                        :class="buyerType === 'member' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600'"
                        class="flex-1 py-2 rounded-lg font-medium text-sm transition-colors">
                    🎫 {{ __('messages.pos.member') }}
                </button>
            </div>
            
            <!-- Member Input -->
            <div x-show="buyerType === 'member'" x-transition class="mt-2 relative">
                <div class="flex gap-2">
                    <input type="text" x-model="memberId" @input="searchMember()" 
                           @keydown.escape="showMemberDropdown = false"
                           placeholder="{{ __('messages.pos.member_search_placeholder') }}" 
                           class="flex-1 form-input py-2 text-sm"
                           :class="memberData ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : ''">
                    <button @click="openQRScanner()" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" title="Scan QR Code">
                        📷
                    </button>
                </div>

                <!-- Autocomplete Dropdown -->
                <div x-show="showMemberDropdown && memberMatches.length > 0" 
                     @click.outside="showMemberDropdown = false"
                     x-transition
                     class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                    <ul class="py-1">
                        <template x-for="match in memberMatches" :key="match.id">
                            <li @click="selectMember(match)" 
                                class="px-4 py-2 hover:bg-primary-50 dark:hover:bg-primary-900/20 cursor-pointer flex justify-between items-center group transition-colors border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white group-hover:text-primary-600 text-sm" x-text="match.name"></div>
                                    <div class="text-xs text-gray-500 font-mono" x-text="match.member_id"></div>
                                </div>
                                <div class="text-xs text-gray-400">{{ __('messages.pos.select') }} →</div>
                            </li>
                        </template>
                    </ul>
                </div>

                <div x-show="memberData" class="mt-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-sm" x-text="memberData?.name?.charAt(0)"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-900 dark:text-white truncate" x-text="memberData?.name"></p>
                        </div>
                        <span class="text-green-600">✓</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-green-200 dark:border-green-700 text-xs" 
                         x-show="['kredit', 'saldo'].includes(paymentMethod)" x-transition>
                        
                        <!-- Credit Info -->
                        <template x-if="paymentMethod === 'kredit'">
                            <div>
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>{{ __('messages.pos.credit_limit') }}</span>
                                    <span x-text="formatRupiah(parseFloat(memberData?.credit_limit || 0))"></span>
                                </div>
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>{{ __('messages.pos.credit_used') }}</span>
                                    <span class="text-orange-600" x-text="formatRupiah(parseFloat(memberData?.credit_used || 0))"></span>
                                </div>
                                <div class="flex justify-between font-medium text-green-700 dark:text-green-400">
                                    <span>{{ __('messages.pos.credit_available') }}</span>
                                    <span x-text="formatRupiah(parseFloat(memberData?.credit_available || 0))"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Saldo Info -->
                        <template x-if="paymentMethod === 'saldo'">
                            <div>
                                <div class="flex justify-between font-medium text-purple-700 dark:text-purple-400">
                                    <span>{{ __('messages.pos.saving_balance') }}</span>
                                    <span x-text="formatRupiah(parseFloat(memberData?.balance || 0))"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <p x-show="memberId && !memberData && !searchingMember" class="text-xs text-red-500 mt-1">{{ __('messages.pos.member_not_found') }}</p>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-white dark:bg-gray-800">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-700 p-2 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-sm text-gray-900 dark:text-gray-100 truncate" x-text="item.name"></h4>
                        <p class="text-xs text-gray-600 dark:text-gray-300" x-text="formatRupiah(item.price)"></p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button @click="updateQty(index, -1)" class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 hover:bg-red-50 dark:hover:bg-red-900 hover:border-red-300 text-gray-700 dark:text-gray-200 flex items-center justify-center font-bold text-sm transition-colors">−</button>
                        <span class="w-8 text-center font-bold text-sm text-gray-900 dark:text-gray-100" x-text="item.qty"></span>
                        <button @click="updateQty(index, 1)" class="w-7 h-7 rounded-full bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 hover:bg-green-50 dark:hover:bg-green-900 hover:border-green-300 text-gray-700 dark:text-gray-200 flex items-center justify-center font-bold text-sm transition-colors">+</button>
                    </div>
                    <div class="w-20 text-right">
                        <span class="font-bold text-sm text-primary-600 dark:text-primary-400" x-text="formatRupiah(item.price * item.qty)"></span>
                    </div>
                    <button @click="cart.splice(index, 1)" class="p-1 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/50 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                <span class="text-4xl block mb-2">🛒</span>
                <p class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.empty_cart') }}</p>
                <p class="text-xs mt-1 text-gray-500">{{ __('messages.pos.empty_cart_desc') }}</p>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 space-y-3">
            <!-- Summary -->
            <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.total_items') }}</span>
                <span class="font-medium text-gray-900 dark:text-white" x-text="cartTotalQty + ' pcs'"></span>
            </div>
            <div class="flex justify-between text-xl font-bold">
                <span class="text-gray-900 dark:text-white">{{ __('messages.pos.total') }}</span>
                <span class="text-primary-600" x-text="formatRupiah(cartTotalAmount)"></span>
            </div>

            <!-- Payment Method -->
            <div class="flex flex-wrap gap-2">
                <button @click="paymentMethod = 'cash'" 
                        :class="paymentMethod === 'cash' ? 'bg-primary-600 text-white border-primary-600 font-bold' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                        class="flex-1 min-w-[70px] py-2.5 rounded-xl border font-medium text-sm transition-colors flex items-center justify-center gap-1">
                    💵 {{ __('messages.pos.cash') }}
                </button>
                <button @click="paymentMethod = 'qris'" 
                        :class="paymentMethod === 'qris' ? 'bg-primary-600 text-white border-primary-600 font-bold' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                        class="flex-1 min-w-[70px] py-2.5 rounded-xl border font-medium text-sm transition-colors flex items-center justify-center gap-1">
                    📱 {{ __('messages.pos.qris') }}
                </button>
                <button x-show="buyerType === 'member' && memberData" @click="paymentMethod = 'saldo'" 
                        :class="paymentMethod === 'saldo' ? 'bg-primary-600 text-white border-primary-600 font-bold' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                        class="flex-1 min-w-[70px] py-2.5 rounded-xl border font-medium text-sm transition-colors flex items-center justify-center gap-1">
                    💳 {{ __('messages.pos.balance') }}
                </button>
                <button x-show="buyerType === 'member' && memberData" @click="paymentMethod = 'kredit'" 
                        :class="paymentMethod === 'kredit' ? 'bg-primary-600 text-white border-primary-600 font-bold' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600'"
                        class="flex-1 min-w-[70px] py-2.5 rounded-xl border font-medium text-sm transition-colors flex items-center justify-center gap-1">
                    📝 {{ __('messages.pos.credit') }}
                </button>
            </div>

            <!-- Cash Input -->
            <div x-show="paymentMethod === 'cash'" x-transition>
                <label class="text-xs text-gray-600 dark:text-gray-400 font-medium">{{ __('messages.pos.cash_amount') }}</label>
                <input type="number" x-model="paidAmount" class="form-input py-2.5 text-right font-bold text-lg text-gray-900 dark:text-white bg-white dark:bg-gray-800" placeholder="0">
                <div x-show="changeAmount >= 0 && paidAmount > 0" class="flex justify-between mt-2 p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <span class="text-green-700 dark:text-green-400 font-medium">{{ __('messages.pos.change') }}</span>
                    <span class="text-green-700 dark:text-green-400 font-bold" x-text="formatRupiah(changeAmount)"></span>
                </div>
            </div>

            <!-- Credit Info -->
            <div x-show="paymentMethod === 'kredit'" x-transition class="p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg">
                <p class="text-sm text-orange-700 dark:text-orange-300 font-medium">📝 {{ __('messages.pos.credit_payment') }}</p>
                <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">{{ __('messages.pos.credit_note') }}</p>
                <div x-show="memberData && cartTotalAmount > (memberData?.credit_available || 0)" class="mt-2 p-2 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded text-xs text-red-600 dark:text-red-400">
                    {{ __('messages.pos.insufficient_credit') }} {{ __('messages.pos.available') }}: <span x-text="formatRupiah(memberData?.credit_available || 0)"></span>
                </div>
            </div>

            <!-- Saldo Warning -->
            <div x-show="paymentMethod === 'saldo'" x-transition class="p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                <p class="text-sm text-purple-700 dark:text-purple-300 font-medium">💳 {{ __('messages.pos.balance_payment') }}</p>
                <div x-show="memberData && cartTotalAmount > (memberData?.balance || 0)" class="mt-2 p-2 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded text-xs text-red-600 dark:text-red-400">
                    {{ __('messages.pos.insufficient_balance') }} {{ __('messages.pos.available') }}: <span x-text="formatRupiah(memberData?.balance || 0)"></span>
                </div>
                <div x-show="memberData && cartTotalAmount <= (memberData?.balance || 0)" class="mt-2 text-xs text-purple-600 dark:text-purple-400">
                    {{ __('messages.pos.balance_note') }}
                </div>
            </div>

            <!-- Pay Button -->
            <button @click="processPayment()" 
                    :disabled="cart.length === 0 || processing || 
                               (paymentMethod === 'cash' && paidAmount < cartTotalAmount) || 
                               ((paymentMethod === 'saldo' || paymentMethod === 'kredit') && !memberData) || 
                               (paymentMethod === 'kredit' && cartTotalAmount > (memberData?.credit_available || 0)) ||
                               (paymentMethod === 'saldo' && cartTotalAmount > (memberData?.balance || 0))" 
                    class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-lg shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none transition-all">
                <span x-show="!processing" x-text="paymentMethod === 'kredit' ? '📝 {{ __('messages.pos.process_credit') }}' : '💰 {{ __('messages.pos.pay_now') }}'"></span>
                <span x-show="processing" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    {{ __('messages.pos.processing') }}
                </span>
            </button>
        </div>
    </div>

    <!-- Hidden File Input for Image Upload -->
    <input type="file" x-ref="imageInput" class="hidden" accept="image/*" @change="handleImageUpload($event)">
</div>

<!-- Receipt Modal -->
<div x-show="$store.receipt.show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="$store.receipt.show = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto">
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.pos.success') }}</h3>
                        <p class="text-sm text-gray-500" x-text="$store.receipt.data.invoice"></p>
                    </div>
                </div>
                <button @click="$store.receipt.show = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 max-h-[400px] overflow-y-auto" id="print-receipt" style="scrollbar-width: thin;">
                <div class="text-center mb-4 pb-4 border-b-2 border-dashed border-gray-300 dark:border-gray-600">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">KOPERASI MART</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Jl. Contoh Alamat No. 123</p>
                </div>
                <div class="text-sm space-y-1 mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.invoice') }}</span><span class="text-gray-900 dark:text-white" x-text="$store.receipt.data.invoice"></span></div>
                    <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.date') }}</span><span class="text-gray-900 dark:text-white" x-text="$store.receipt.data.date"></span></div>
                    <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.cashier') }}</span><span class="text-gray-900 dark:text-white" x-text="$store.receipt.data.cashier"></span></div>
                    <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.buyer') }}</span><span class="text-gray-900 dark:text-white" x-text="$store.receipt.data.buyer"></span></div>
                    <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.method') }}</span><span class="uppercase text-gray-900 dark:text-white" x-text="$store.receipt.data.method"></span></div>
                </div>
                <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
                    <template x-for="item in $store.receipt.data.items" :key="item.id">
                        <div class="mb-2">
                            <div class="font-medium text-gray-900 dark:text-white" x-text="item.name"></div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span x-text="item.qty + ' x Rp ' + item.price.toLocaleString('id-ID')"></span>
                                <span class="text-gray-900 dark:text-white" x-text="'Rp ' + (item.qty * item.price).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span class="text-gray-900 dark:text-white">{{ __('messages.pos.total_receipt') }}</span>
                        <span class="text-primary-600" x-text="'Rp ' + $store.receipt.data.total.toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between"><span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.paid_receipt') }}</span><span class="text-gray-900 dark:text-white" x-text="'Rp ' + $store.receipt.data.paid.toLocaleString('id-ID')"></span></div>
                    <div class="flex justify-between font-bold text-green-600"><span>{{ __('messages.pos.change_receipt') }}</span><span x-text="'Rp ' + $store.receipt.data.change.toLocaleString('id-ID')"></span></div>
                </div>
                <div class="text-center mt-6 pt-4 border-t-2 border-dashed border-gray-300 dark:border-gray-600">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.pos.thank_you') }}</p>
                </div>
            </div>
            <div class="flex gap-3 p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-2xl">
                <button @click="$store.receipt.show = false; location.reload();" class="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-xl font-medium hover:bg-gray-300 transition-colors">
                    🔄 {{ __('messages.pos.new_transaction') }}
                </button>
                <button @click="printReceipt()" class="flex-1 px-4 py-3 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition-colors">
                    🖨️ {{ __('messages.pos.print_receipt') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- QRIS Modal -->
<div x-show="$store.qris.show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="$store.qris.show = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-auto text-center p-6">
            
            <div class="mb-6">
                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-2">{{ __('messages.pos.scan_qris') }}</h3>
                <p class="text-gray-500 dark:text-gray-400">{{ __('messages.pos.scan_qris_desc') }}</p>
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow-inner border border-gray-200 inline-block mb-6">
                @if(isset($qrisImage))
                    <img src="{{ Storage::url($qrisImage) }}" alt="QRIS Code" class="w-64 h-64 object-contain">
                @else
                    <div class="w-64 h-64 flex items-center justify-center bg-gray-100 text-gray-400 rounded">
                        <div class="text-center">
                            <p class="text-4xl mb-2">📷</p>
                            <p class="text-sm">{{ __('messages.pos.qris_not_set') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-left bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-6 border border-blue-100 dark:border-blue-800">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600 dark:text-gray-400">{{ __('messages.pos.total_payment') }}</span>
                    <span class="font-bold text-blue-700 dark:text-blue-400" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format($data.cartTotalAmount)"></span>
                </div>
                <p class="text-xs text-blue-600 dark:text-blue-300 mt-2">
                    {{ __('messages.pos.qris_note') }}
                </p>
            </div>

            <div class="flex gap-3">
                <button @click="$store.qris.show = false" class="flex-1 py-3 px-4 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-medium transition-colors">
                    {{ __('messages.pos.cancel') }}
                </button>
                <button @click="$store.qris.show = false; processPayment(true)" class="flex-1 py-3 px-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 dark:shadow-none transition-colors">
                    {{ __('messages.pos.confirm_payment') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scanner Modal -->
<div x-show="isScanning" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="stopScanner()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg mx-auto p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.pos.scan_product') }}</h3>
                <button @click="stopScanner()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="relative rounded-xl overflow-hidden bg-black text-white aspect-[4/3]">
                <div id="reader" class="w-full h-full"></div>
                <!-- Scan Overlay Line -->
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                    <div class="w-3/4 h-px bg-red-500 shadow-[0_0_10px_rgba(255,0,0,0.8)] animate-pulse"></div>
                </div>
            </div>
            <div x-show="lastScannedCode" x-transition.opacity.duration.500ms class="mt-4 text-center p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                {{ __('messages.pos.scanned_success') }}: <span class="font-bold" x-text="lastScannedCode"></span>
            </div>
            <p class="text-center text-sm text-gray-500 mt-4">{{ __('messages.pos.scan_instruction') }}</p>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('receipt', {
        show: false,
        data: { invoice: '', date: '', cashier: '', buyer: '', method: '', items: [], total: 0, paid: 0, change: 0 }
    });
    Alpine.store('qris', {
        show: false
    });

    Alpine.data('posSystem', () => ({
        products: @json($products).map(p => ({...p, image_error: false})),
        search: '',
        selectedCategory: '',
        currentPage: 1,
        perPage: 16,
        cart: [],
        buyerType: 'umum',
        memberId: '',
        memberData: null,
        searchingMember: false,
        paymentMethod: 'cash',
        paidAmount: 0,
        processing: false,
        processedImageId: null,

        // Scanner State
        scanner: null,
        isScanning: false,
        lastScannedCode: null,
        scanAudio: new Audio('/sounds/beep.mp3'), // Ensure this file exists or use a data URI

        startScanner() {
            this.isScanning = true;
            this.lastScannedCode = null;
            
            this.$nextTick(() => {
                if (!this.scanner) {
                    this.scanner = new Html5QrcodeScanner("reader", { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.333333
                    }, false); // verbose=false
                }
                
                this.scanner.render(this.onScanSuccess.bind(this), (error) => {
                    // Ignore scan errors as they happen every frame if no code detected
                });
            });
        },

        onScanSuccess(decodedText, decodedResult) {
            if (this.lastScannedCode === decodedText) return; // Prevent double scan of same frame

            this.lastScannedCode = decodedText;
            
            // Play Beep
            // this.scanAudio.play().catch(e => console.log('Audio play failed', e));

            // Search and Add
            const exactMatch = this.products.find(p => p.code && p.code.toLowerCase() === decodedText.toLowerCase());
            
            if (exactMatch) {
                this.addToCart(exactMatch);
                // Optional: Flash screen or show notification
            } else {
                // If not found, maybe just set search?
                this.search = decodedText;
            }

            // Reset last scanned after delay to allow re-scanning same item
            setTimeout(() => {
                this.lastScannedCode = null;
            }, 2000);
        },

        stopScanner() {
            this.isScanning = false;
            if (this.scanner) {
                this.scanner.clear().catch(error => {
                    console.error("Failed to clear html5-qrcode scanner. ", error);
                });
                // We don't destroy instance to reuse it faster? 
                // Or clear() removes the UI, so we might need to new it again or just render()
                // doc says clear() stops scanning and clears UI.
            }
        },

        handleBarcodeScan() {
            if (!this.search) return;
            const s = this.search.trim().toLowerCase();
            
            // 1. Exact Code Match
            const exactMatch = this.products.find(p => p.code && p.code.toLowerCase() === s);
            if (exactMatch) {
                this.addToCart(exactMatch);
                this.search = '';
                return;
            }

            // 2. Single Filter Result
            if (this.filteredProducts.length === 1) {
                this.addToCart(this.filteredProducts[0]);
                this.search = '';
                return;
            }
        },

        triggerImageUpload(id) {
            this.processedImageId = id;
            this.$refs.imageInput.value = '';
            this.$refs.imageInput.click();
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file || !this.processedImageId) return;

            // Optimistic update or waiting indicator could go here
            const productId = this.processedImageId;
            const productIndex = this.products.findIndex(p => p.id === productId);
            
            if (productIndex === -1) return;

            const formData = new FormData();
            formData.append('image', file);
            
            // Show loading state on the specific product (optional, but good UX)
            // For now, we rely on the fast response or maybe a toast

            fetch(`/products/${productId}/image`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async r => {
                const isJson = r.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await r.json() : null;
                
                if (!r.ok) {
                    const errorMsg = data?.message || `Server error: ${r.status}`;
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(data => {
                if (data && data.success) {
                    // Update main products array
                    // The view expects paths relative to /storage/ OR absolute URLs
                    // We extract relative path to stay consistent with existing logic if possible
                    let finalPath = data.image_url;
                    
                    if (finalPath.includes('/storage/')) {
                        finalPath = finalPath.split('/storage/').pop();
                    } else if (finalPath.includes('http') && finalPath.includes('storage')) {
                        // Handle potential full APP_URL/storage/path
                        finalPath = finalPath.split('/storage/').pop();
                    }

                    this.products[productIndex].image = finalPath + '?t=' + new Date().getTime(); 
                    
                    // Optional: show a small toast or success indicator
                    console.log('Image updated successfully:', finalPath);
                } else {
                    alert('{{ __('messages.pos.upload_failed') }} ' + (data?.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                alert('{{ __('messages.pos.upload_error') }}: ' + err.message);
            });
        },
        // Placeholder Colors
        placeholderColors: [
            'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
            'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
            'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
            'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
            'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
            'bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400',
            'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
            'bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400'
        ],

        getPlaceholderColor(id) {
            return this.placeholderColors[id % this.placeholderColors.length];
        },

        getInitials(name) {
            if (!name) return '??';
            return name.substring(0, 2).toUpperCase();
        },

        // Member Autocomplete
        memberMatches: [],
        showMemberDropdown: false,

        get filteredProducts() {
            let result = this.products;
            if (this.search) {
                const s = this.search.toLowerCase();
                result = result.filter(p => p.name.toLowerCase().includes(s) || p.code.toLowerCase().includes(s));
            }
            if (this.selectedCategory) {
                result = result.filter(p => p.category_id == this.selectedCategory);
            }
            return result;
        },

        get totalPages() {
            return Math.ceil(this.filteredProducts.length / this.perPage);
        },

        get paginatedProducts() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredProducts.slice(start, start + this.perPage);
        },

        get cartTotalQty() { return this.cart.reduce((s, i) => s + i.qty, 0); },
        get cartTotalAmount() { return this.cart.reduce((s, i) => s + (i.price * i.qty), 0); },
        get changeAmount() { return Math.max(0, this.paidAmount - this.cartTotalAmount); },

        formatRupiah(n) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n); },

        addToCart(product, event) {
            if (product.stock <= 0) { alert('{{ __('messages.pos.out_of_stock') }}'); return; }
            
            // Trigger Animation
            if (event) {
                this.animateFlyToCart(event.currentTarget, product);
            }

            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) existing.qty++;
                else alert('{{ __('messages.pos.insufficient_stock') }}');
            } else {
                this.cart.push({ id: product.id, name: product.name, price: product.price, qty: 1, stock: product.stock });
            }
        },

        animateFlyToCart(startEl, product) {
            const container = document.getElementById('flying-container');
            const cartTarget = document.getElementById('cart-target');
            if (!container || !cartTarget) return;

            // Get start position
            const startRect = startEl.querySelector('.aspect-square').getBoundingClientRect();
            
            // Get end position
            const endRect = cartTarget.getBoundingClientRect();

            // Create flying element
            const flyingEl = document.createElement('div');
            flyingEl.className = 'absolute rounded-lg shadow-xl border-2 border-primary-500 overflow-hidden z-[100]';
            flyingEl.style.width = startRect.width + 'px';
            flyingEl.style.height = startRect.height + 'px';
            flyingEl.style.left = startRect.left + 'px';
            flyingEl.style.top = startRect.top + 'px';
            flyingEl.style.transition = 'all 0.6s cubic-bezier(0.2, 0.8, 0.2, 1)'; // Smooth curve
            flyingEl.style.backgroundImage = product.image ? `url('/storage/${product.image}')` : '';
            flyingEl.style.backgroundSize = 'cover';
            flyingEl.style.backgroundColor = product.image ? 'white' : this.getPlaceholderColor(product.id).split(' ')[0].replace('bg-', ''); // simple fallback color fetch might be tricky, use default
            
            // If no image, add text content clone? simplified: just use a color box
            if (!product.image) {
                flyingEl.className += ' flex items-center justify-center font-bold text-white text-2xl';
                flyingEl.innerHTML = this.getInitials(product.name);
                // Extract bg color class or just use primary
                flyingEl.style.backgroundColor = '#4f46e5'; 
            }

            container.appendChild(flyingEl);

            // Force reflow
            void flyingEl.offsetWidth;

            // Animate to target
            flyingEl.style.width = '20px';
            flyingEl.style.height = '20px';
            flyingEl.style.left = (endRect.left + endRect.width/2) + 'px';
            flyingEl.style.top = (endRect.top + endRect.height/2) + 'px';
            flyingEl.style.opacity = '0.5';
            flyingEl.style.borderRadius = '50%';

            // Cleanup
            setTimeout(() => {
                flyingEl.remove();
            }, 600);
        },

        updateQty(index, change) {
            const item = this.cart[index];
            const newQty = item.qty + change;
            if (newQty > item.stock) { alert('{{ __('messages.pos.insufficient_stock') }}'); return; }
            if (newQty <= 0) this.cart.splice(index, 1);
            else item.qty = newQty;
        },

        searchMember() {
            if (!this.memberId || this.memberId.length < 2) { 
                this.memberData = null; 
                this.memberMatches = [];
                this.showMemberDropdown = false;
                return; 
            }
            
            this.searchingMember = true;
            
            fetch('/api/members/search?q=' + encodeURIComponent(this.memberId))
                .then(r => r.json())
                .then(data => { 
                    this.memberData = data.member || null; 
                    this.memberMatches = data.matches || [];
                    this.showMemberDropdown = this.memberMatches.length > 0 && !this.memberData;
                    this.searchingMember = false; 
                })
                .catch(() => { 
                   this.memberData = null; 
                   this.memberMatches = [];
                   this.searchingMember = false; 
                });
        },
        
        selectMember(member) {
            this.memberId = member.member_id;
            this.memberMatches = []; 
            this.showMemberDropdown = false;
            this.searchMember(); // Force exact match search
        },

        openQRScanner() {
            alert('{{ __('messages.pos.qr_scan_note') }}');
        },
        
        showQris() {
            Alpine.store('qris').show = true;
        },

        processPayment(isQrisConfirmed = false) {
            if (this.cart.length === 0) return;
            
            // If method is QRIS and not yet confirmed, show the modal first
            if (this.paymentMethod === 'qris' && !isQrisConfirmed) {
                this.showQris();
                return;
            }

            this.processing = true;

            const cartCopy = [...this.cart];
            const total = this.cartTotalAmount;
            const paid = this.paymentMethod === 'cash' ? parseFloat(this.paidAmount) : total;
            const change = paid - total;

            fetch('{{ route("pos.store") }}', {
                method: 'POST',

                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    items: this.cart,
                    payment_method: this.paymentMethod,
                    paid_amount: paid,
                    buyer_type: this.buyerType,
                    member_id: this.buyerType === 'member' ? this.memberData?.id : null
                })
            })
            .then(r => r.json())
            .then(data => {
                this.processing = false;
                if (data.success) {
                    Alpine.store('receipt').data = {
                        id: data.transaction_id,
                        invoice: data.invoice,
                        date: new Date().toLocaleString('id-ID'),
                        cashier: '{{ auth()->user()->name }}',
                        buyer: this.buyerType === 'member' ? this.memberData?.name : '{{ __('messages.pos.general') }}',
                        method: this.paymentMethod,
                        items: cartCopy,
                        total: total,
                        paid: paid,
                        change: change
                    };
                    Alpine.store('receipt').show = true;
                    this.cart = [];
                    this.paidAmount = 0;
                    this.memberId = '';
                    this.memberData = null;
                    this.memberMatches = [];
                    this.showMemberDropdown = false;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(e => { this.processing = false; alert('{{ __('messages.pos.transaction_error') }}'); console.error(e); });
        }
    }));
});

function printReceipt() {
    const id = Alpine.store('receipt').data.id;
    if (id) {
        // Open the dedicated 58mm receipt page
        const win = window.open('/pos/receipt/' + id, '_blank', 'width=400,height=600');
        // The receipt page has auto-print script, but we can double check
        if (win) {
            win.onload = function() { win.print(); }
        }
    } else {
        alert('ID Transaksi tidak ditemukan, silakan cetak dari Riwayat.');
    }
}
</script>
@endpush
@endsection
