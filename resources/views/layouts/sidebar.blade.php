<!-- Sidebar -->
<aside :class="{ 
        'translate-x-0': sidebarMobileOpen, 
        '-translate-x-full lg:translate-x-0': !sidebarMobileOpen,
        'lg:w-72': sidebarOpen,
        'lg:w-20': !sidebarOpen
    }"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-all duration-300 ease-in-out lg:translate-x-0">
    
    <!-- Logo Section -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-700 flex items-center justify-center shadow-sm overflow-hidden border border-gray-100 dark:border-gray-600 flex-shrink-0">
                @if(isset($globalSettings['coop_logo']) && $globalSettings['coop_logo'])
                    <img src="{{ Storage::url($globalSettings['coop_logo']) }}" alt="{{ $globalSettings['coop_name'] ?? 'Logo' }}" class="w-full h-full object-contain p-1">
                @else
                    <div class="h-full w-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr($globalSettings['coop_name'] ?? 'K', 0, 1)) }}
                    </div>
                @endif
            </div>
            <span x-show="sidebarOpen" x-transition class="font-bold text-gray-900 dark:text-white text-lg truncate max-w-[11rem]">
                {{ $globalSettings['coop_name'] ?? 'Koperasi' }}
            </span>
        </a>
        
        <!-- Toggle Sidebar (Desktop) -->
        <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg x-show="sidebarOpen" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
            <svg x-show="!sidebarOpen" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
        </button>
        
        <!-- Close Mobile Sidebar -->
        <button @click="sidebarMobileOpen = false" class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin" x-data="{ openGroup: '{{ 
        request()->routeIs('savings.*') || request()->routeIs('loans.*') || request()->routeIs('loan-payments.*') || request()->routeIs('withdrawals.*') || request()->routeIs('shu.*') ? 'finance' : 
        (request()->routeIs('pos.*') ? 'sales' : 
        (request()->routeIs('categories.*') || request()->routeIs('products.*') || request()->routeIs('inventory.*') || request()->routeIs('suppliers.*') || request()->routeIs('purchases.*') || request()->routeIs('stock-opname.*') ? 'inventory' : 
        (request()->routeIs('expenses.*') || request()->routeIs('vouchers.*') || request()->routeIs('consignment.*') ? 'operations' : 
        (request()->routeIs('shop.*') ? 'shop' : 
        (request()->routeIs('reports.*') || request()->routeIs('announcements.*') || request()->routeIs('ad-art') || request()->routeIs('documentation') || request()->routeIs('uat') || request()->routeIs('polls.*') || request()->routeIs('aspirations.*') ? 'reports' : 
        (request()->routeIs('organization.*') || request()->routeIs('documents.*') || request()->routeIs('journals.*') || request()->routeIs('members.*') || request()->routeIs('reconciliation.*') || request()->routeIs('establishment') || request()->routeIs('governance') ? 'organization' : 
        (request()->routeIs('import.*') || request()->routeIs('members.*') || request()->routeIs('master-data.*') || request()->routeIs('roles.*') || request()->routeIs('settings.*') ? 'admin' : ''))))))) 
    }}' }">
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="{{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-inactive' }} mb-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.dashboard') }}</span>
        </a>



        <!-- Keuangan Group -->
        <div class="mb-2">
            <button @click="openGroup === 'finance' ? openGroup = null : openGroup = 'finance'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'finance' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.finance') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'finance' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'finance' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('savings.index') }}" class="{{ request()->routeIs('savings.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('savings.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.savings') }}
                </a>
                <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.index') || request()->routeIs('loans.show') || request()->routeIs('loans.create') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('loans.index') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.loans') }}
                </a>
                <a href="{{ route('loans.simulation') }}" class="{{ request()->routeIs('loans.simulation') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('loans.simulation') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Simulasi Pinjaman
                </a>
                <a href="{{ route('loan-payments.index') }}" class="{{ request()->routeIs('loan-payments.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('loan-payments.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.loan_payments') }}
                </a>
                <a href="{{ route('withdrawals.index') }}" class="{{ request()->routeIs('withdrawals.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('withdrawals.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.withdrawals') }}
                </a>
                <a href="{{ auth()->user()->hasAdminAccess() ? route('shu.index') : route('shu.my-shu') }}" class="{{ request()->routeIs('shu.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('shu.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.shu') }}
                </a>
            </div>
        </div>
        
        <!-- Koperasi Mart Group (Access Rights retained) -->
        @if(auth()->user()->hasStoreAccess())
            
        <!-- 1. Group Sales (Kasir) -->
        <div class="mb-2">
            <button @click="openGroup === 'sales' ? openGroup = null : openGroup = 'sales'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'sales' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.sales') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'sales' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'sales' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.index') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pos.index') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.pos') }}
                </a>
                <a href="{{ route('pos.history') }}" class="{{ request()->routeIs('pos.history') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pos.history') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.sales_history') }}
                </a>
                <a href="{{ route('pos.credits') }}" class="{{ request()->routeIs('pos.credits') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pos.credits') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.credit_reports') }}
                </a>
                <a href="{{ route('pos.scan') }}" class="{{ request()->routeIs('pos.scan') || request()->routeIs('pos.manage') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pos.scan') || request()->routeIs('pos.manage') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    📷 {{ __('messages.sidebar.scan_orders') }}
                </a>
            </div>
        </div>

        <!-- 2. Group Inventory (Gudang) -->
        <div class="mb-2">
            <button @click="openGroup === 'inventory' ? openGroup = null : openGroup = 'inventory'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'inventory' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.inventory') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'inventory' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'inventory' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('products.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.products') }}
                </a>
                @php $lowStockCount = \App\Models\Product::lowStock()->count(); @endphp
                <a href="{{ route('inventory.low-stock') }}" class="{{ request()->routeIs('inventory.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center justify-between gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('inventory.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                        {{ __('messages.sidebar.low_stock') }}
                    </div>
                    @if($lowStockCount > 0)
                    <span class="px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400 rounded-full">{{ $lowStockCount }}</span>
                    @endif
                </a>
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('categories.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.categories') }}
                </a>
                <a href="{{ route('stock-opname.index') }}" class="{{ request()->routeIs('stock-opname.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('stock-opname.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.stock_opname') }}
                </a>
                <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('suppliers.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.suppliers') }}
                </a>
                <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('purchases.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.purchases') }}
                </a>
            </div>
        </div>

        <!-- 3. Group Operations (Biaya & Konsinyasi) -->
        <div class="mb-2">
            <button @click="openGroup === 'operations' ? openGroup = null : openGroup = 'operations'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'operations' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.operations') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'operations' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'operations' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.index') || request()->routeIs('expenses.create') || request()->routeIs('expenses.show') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('expenses.index') || request()->routeIs('expenses.create') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.expenses') }}
                </a>
                <a href="{{ route('expenses.categories.index') }}" class="{{ request()->routeIs('expenses.categories.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('expenses.categories.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.expense_categories') }}
                </a>
                
                <div class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-2">
                    {{ __('messages.sidebar.consignment') }}
                </div>
                
                <a href="{{ route('consignment.inbounds.index') }}" class="{{ request()->routeIs('consignment.inbounds.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('consignment.inbounds.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.consignment_inbound') }}
                </a>
                <a href="{{ route('consignment.settlements.index') }}" class="{{ request()->routeIs('consignment.settlements.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('consignment.settlements.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.consignment_settlement') }}
                </a>
                <a href="{{ url('guide-consignment.html') }}" target="_blank" class="text-blue-500 dark:text-blue-400 flex items-center gap-2 p-2 text-sm rounded-lg hover:text-blue-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('messages.sidebar.consignment_guide') }}
                </a>
                
                <div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

                <a href="{{ route('vouchers.index') }}" class="{{ request()->routeIs('vouchers.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('vouchers.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.vouchers') }}
                </a>
            </div>
        </div>
        @endif

        <!-- Belanja (Member) Group -->
        <div class="mb-2">
            <button @click="openGroup === 'shop' ? openGroup = null : openGroup = 'shop'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'shop' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.shop') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'shop' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'shop' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.index') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('shop.index') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.browse') }}
                </a>
                <a href="{{ route('shop.history') }}" class="{{ request()->routeIs('shop.history') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('shop.history') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.order_history') }}
                </a>
            </div>
        </div>

        <!-- Laporan Group -->
        <div class="mb-2">
            <button @click="openGroup === 'reports' ? openGroup = null : openGroup = 'reports'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'reports' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.reports') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'reports' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'reports' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') && !request()->routeIs('reports.financial-health') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.*') && !request()->routeIs('reports.financial-health') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.all_reports') }}
                </a>
                @if(auth()->user()->hasAdminAccess())
                <a href="{{ route('reports.financial-health') }}" class="{{ request()->routeIs('reports.financial-health') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.financial-health') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    📊 Kesehatan Keuangan
                </a>
                @endif
                <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('announcements.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.announcements') }}
                </a>
                <a href="{{ route('ad-art') }}" class="{{ request()->routeIs('ad-art') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('ad-art') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.ad_art') }}
                </a>


                <a href="{{ route('documentation') }}" class="{{ request()->routeIs('documentation') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('documentation') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.documentation') }}
                </a>
                <a href="{{ route('uat') }}" class="{{ request()->routeIs('uat') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('uat') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.uat') }}
                </a>
                <a href="{{ route('polls.index') }}" class="{{ request()->routeIs('polls.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('polls.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.polls') }}
                </a>
                <a href="{{ route('aspirations.index') }}" class="{{ request()->routeIs('aspirations.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('aspirations.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ auth()->user()->hasAdminAccess() ? __('messages.sidebar.aspiration_results') : __('messages.sidebar.aspirations') }}
                </a>
            </div>
        </div>

        <!-- Group: Manajemen Organisasi (Accessible to Admin & Pengurus) -->
        @if(auth()->user()->hasAdminAccess())
        <div class="mb-2">
            <button @click="openGroup === 'organization' ? openGroup = null : openGroup = 'organization'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'organization' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.organization') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'organization' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'organization' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('organization.index') }}" class="{{ request()->routeIs('organization.index') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('organization.index') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Dashboard
                </a>
                <a href="{{ route('establishment') }}" class="{{ request()->routeIs('establishment') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('establishment') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Panduan Pendirian
                </a>
                <a href="{{ route('governance') }}" class="{{ request()->routeIs('governance') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('governance') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.governance') }}
                </a>
                <a href="{{ route('members.index') }}" class="{{ request()->routeIs('members.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('members.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Daftar Anggota
                </a>
                <a href="{{ route('organization.assets') }}" class="{{ request()->routeIs('organization.assets') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('organization.assets') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Inventaris Aset
                </a>
                <a href="{{ route('organization.meetings') }}" class="{{ request()->routeIs('organization.meetings') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('organization.meetings') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Notulen Rapat
                </a>
                <a href="{{ route('organization.profiles') }}" class="{{ request()->routeIs('organization.profiles') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('organization.profiles') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Daftar Pengurus
                </a>
                <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('documents.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.documents') }}
                </a>
                <a href="{{ route('document-templates.index') }}" class="{{ request()->routeIs('document-templates.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('document-templates.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Template Dokumen
                </a>
                <a href="{{ route('journals.index') }}" class="{{ request()->routeIs('journals.index', 'journals.show', 'journals.create') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('journals.index', 'journals.show', 'journals.create') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.journals') }}
                </a>
                <a href="{{ route('reconciliation.index') }}" class="{{ request()->routeIs('reconciliation.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reconciliation.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Rekonsiliasi Bank
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user()->isAdmin())
        <!-- Admin System Group -->
        <div class="mb-2">
            <button @click="openGroup === 'admin' ? openGroup = null : openGroup = 'admin'" 
                    class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-gray-600 dark:text-gray-300"
                    :class="openGroup === 'admin' ? 'bg-gray-50 dark:bg-gray-700/50 font-medium text-primary-600 dark:text-primary-400' : ''">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>{{ __('messages.sidebar.admin') }}</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="openGroup === 'admin' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div x-show="openGroup === 'admin' && sidebarOpen" x-collapse class="pl-4 mt-1 space-y-1">
                <a href="{{ route('master-data.index') }}" class="{{ request()->routeIs('master-data.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('master-data.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.master_data') }}
                </a>
                <a href="{{ route('import.index') }}" class="{{ request()->routeIs('import.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('import.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.import') }}
                </a>

                <div class="my-2 border-t border-gray-100 dark:border-gray-700"></div>
                
                <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('roles.*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.roles') }}
                </a>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('settings.index') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.settings') }}
                </a>
                <a href="{{ route('settings.landing') }}" class="{{ request()->routeIs('settings.landing*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('settings.landing*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    Landing Page
                </a>
                <a href="{{ route('settings.backup') }}" class="{{ request()->routeIs('settings.backup*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('settings.backup*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.backup') }}
                </a>
                <a href="{{ route('settings.audit-logs') }}" class="{{ request()->routeIs('settings.audit-logs*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('settings.audit-logs*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    {{ __('messages.sidebar.audit_logs') }}
                </a>
                <a href="{{ route('settings.payment-gateway') }}" class="{{ request()->routeIs('settings.payment-gateway*') ? 'text-primary-600 dark:text-primary-400 font-medium' : 'text-gray-500 dark:text-gray-400' }} flex items-center gap-2 p-2 text-sm rounded-lg hover:text-primary-600 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('settings.payment-gateway*') ? 'bg-primary-600' : 'bg-gray-400' }}"></span>
                    💳 Payment Gateway
                </a>
            </div>
        </div>
        @endif
    </nav>

    <!-- User Profile Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
        @auth
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div x-show="sidebarOpen" x-transition class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">
                    {{ auth()->user()->role }}
                </p>
            </div>
        </a>
        @endauth
    </div>
</aside>
