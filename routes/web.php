<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LandingSettingController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanPaymentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ManualJournalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\MemberAspirationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// WhatsApp Webhooks
Route::post('/webhook/whatsapp', [App\Http\Controllers\WhatsappWebhookController::class, 'handle'])->name('webhook.whatsapp');
Route::post('/webhook/whatsapp/twilio', [App\Http\Controllers\WhatsappWebhookController::class, 'handleTwilio'])->name('webhook.whatsapp.twilio');

// Payment Gateway Webhook (Midtrans Notification)
Route::post('/webhook/payment', [App\Http\Controllers\PaymentWebhookController::class, 'handle'])->name('payment.webhook');

// Language Switcher
Route::get('/locale/{locale}', [App\Http\Controllers\LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    // Profile Completion (exempt from profile.complete middleware)
    Route::get('/profile/complete', [ProfileController::class, 'showCompleteForm'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileController::class, 'updateComplete'])->name('profile.complete.update');

    // Public Information (Accessible during profile completion)
    Route::get('ad-art', [App\Http\Controllers\InformationController::class, 'adArt'])->name('ad-art');
    Route::get('ad-art/download-pdf', [App\Http\Controllers\InformationController::class, 'downloadAdArtPdf'])->name('ad-art.download-pdf');
    Route::get('governance', [App\Http\Controllers\InformationController::class, 'governance'])->name('governance');
    Route::get('governance/download-pdf', [App\Http\Controllers\InformationController::class, 'downloadGovernancePdf'])->name('governance.download-pdf');
});

// Public Document Verification (No Auth Required)
Route::get('documents/verify/{id}', [App\Http\Controllers\PublicDocumentController::class, 'verify'])->name('documents.verify.public');

Route::middleware(['auth', 'verified', 'active', 'profile.complete'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API for Member Search (POS)
    Route::get('/api/members/search', [MemberController::class, 'apiSearch'])->name('api.members.search');
    
    // AI Chat - Available for all authenticated users
    Route::post('/api/ai/chat', [App\Http\Controllers\AiSettingController::class, 'chat'])->name('ai.chat.public');
    Route::post('/api/ai/test', [App\Http\Controllers\AiSettingController::class, 'testConnection'])->name('ai.test.public');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Push Notifications (PWA)
    Route::prefix('api/push')->group(function () {
        Route::get('/vapid-public-key', [App\Http\Controllers\PushNotificationController::class, 'vapidPublicKey'])->name('push.vapid');
        Route::post('/subscribe', [App\Http\Controllers\PushNotificationController::class, 'subscribe'])->name('push.subscribe');
        Route::post('/unsubscribe', [App\Http\Controllers\PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
        Route::post('/test', [App\Http\Controllers\PushNotificationController::class, 'sendTest'])->name('push.test');
    });

    // Member Management (Roles & Permissions handled by MemberController)
    Route::delete('members/bulk-destroy', [App\Http\Controllers\MemberController::class, 'bulkDestroy'])->name('members.bulk_destroy');
    Route::resource('members', App\Http\Controllers\MemberController::class);
    Route::post('members/{member}/toggle-status', [App\Http\Controllers\MemberController::class, 'toggleStatus'])->name('members.toggle-status');

    // Admin Only Core Routes
    Route::middleware('can:admin')->group(function() {
        // Exports
        Route::get('members/export', [App\Http\Controllers\MemberController::class, 'export'])->name('members.export');
        Route::get('savings/export', [App\Http\Controllers\SavingController::class, 'export'])->name('savings.export');
        Route::get('loans/export', [App\Http\Controllers\LoanController::class, 'export'])->name('loans.export');
        Route::get('loan-payments/export', [LoanPaymentController::class, 'export'])->name('loan-payments.export');

        // Organization Management (Buku-Buku Wajib)
        Route::get('organization', [App\Http\Controllers\OrganizationController::class, 'index'])->name('organization.index');
        Route::get('organization/assets', [App\Http\Controllers\OrganizationController::class, 'assets'])->name('organization.assets');
        Route::post('organization/assets', [App\Http\Controllers\OrganizationController::class, 'storeAsset'])->name('organization.assets.store');
        Route::put('organization/assets/{asset}', [App\Http\Controllers\OrganizationController::class, 'updateAsset'])->name('organization.assets.update');
        Route::delete('organization/assets/{asset}', [App\Http\Controllers\OrganizationController::class, 'destroyAsset'])->name('organization.assets.destroy');

        Route::get('organization/meetings', [App\Http\Controllers\OrganizationController::class, 'meetings'])->name('organization.meetings');
        Route::post('organization/meetings', [App\Http\Controllers\OrganizationController::class, 'storeMeeting'])->name('organization.meetings.store');
        Route::put('organization/meetings/{meeting}', [App\Http\Controllers\OrganizationController::class, 'updateMeeting'])->name('organization.meetings.update');
        Route::delete('organization/meetings/{meeting}', [App\Http\Controllers\OrganizationController::class, 'destroyMeeting'])->name('organization.meetings.destroy');

        Route::get('organization/profiles', [App\Http\Controllers\OrganizationController::class, 'profiles'])->name('organization.profiles');
        Route::post('organization/profiles', [App\Http\Controllers\OrganizationController::class, 'storeProfile'])->name('organization.profiles.store');
        Route::put('organization/profiles/{profile}', [App\Http\Controllers\OrganizationController::class, 'updateProfile'])->name('organization.profiles.update');
        Route::delete('organization/profiles/{profile}', [App\Http\Controllers\OrganizationController::class, 'destroyProfile'])->name('organization.profiles.destroy');

        // Inventory Alerts
        Route::get('inventory/low-stock', [App\Http\Controllers\InventoryController::class, 'lowStock'])->name('inventory.low-stock');

        // Stock Opname
        Route::get('stock-opname', [App\Http\Controllers\StockOpnameController::class, 'index'])->name('stock-opname.index');
        Route::get('stock-opname/create', [App\Http\Controllers\StockOpnameController::class, 'create'])->name('stock-opname.create');
        Route::post('stock-opname', [App\Http\Controllers\StockOpnameController::class, 'store'])->name('stock-opname.store');
        Route::get('stock-opname/{stockOpname}', [App\Http\Controllers\StockOpnameController::class, 'show'])->name('stock-opname.show');
        Route::post('stock-opname/{stockOpname}/complete', [App\Http\Controllers\StockOpnameController::class, 'complete'])->name('stock-opname.complete');
        Route::post('stock-opname/{stockOpname}/cancel', [App\Http\Controllers\StockOpnameController::class, 'cancel'])->name('stock-opname.cancel');
        Route::delete('stock-opname/{stockOpname}', [App\Http\Controllers\StockOpnameController::class, 'destroy'])->name('stock-opname.destroy');
        Route::get('stock-opname/{stockOpname}/export', [App\Http\Controllers\StockOpnameController::class, 'export'])->name('stock-opname.export');
    });

    // Members (Strictly for super-admin)
    Route::middleware('can:super-admin')->group(function() {
        // Route::resource('members', ...) moved to general auth
    });

    // Member Card Features (Accessible to those with card access)
    Route::get('members/{member}/card', [App\Http\Controllers\MemberController::class, 'printCard'])->name('members.card');
    Route::get('members/{member}/digital-card', [App\Http\Controllers\MemberController::class, 'digitalCard'])->name('members.digital-card');
    Route::get('my-credits', [MemberController::class, 'myCredits'])->name('members.credits');
    Route::get('members/{member}/transactions/print', [MemberController::class, 'printTransactionHistory'])->name('members.transactions.print');

    // Savings
    Route::delete('savings/bulk-destroy', [App\Http\Controllers\SavingController::class, 'bulkDestroy'])->name('savings.bulk_destroy');
    Route::resource('savings', App\Http\Controllers\SavingController::class);
    Route::get('savings/{member}/print', [App\Http\Controllers\SavingController::class, 'printBook'])->name('savings.print');

    // Loans
    Route::delete('loans/bulk-destroy', [App\Http\Controllers\LoanController::class, 'bulkDestroy'])->name('loans.bulk_destroy');
    Route::get('loans/simulation', [LoanController::class, 'simulation'])->name('loans.simulation');
    Route::resource('loans', LoanController::class);
    Route::put('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/sign', [LoanController::class, 'storeSignature'])->name('loans.sign');
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');

    // Loan Payments
    Route::delete('loan-payments/bulk-delete', [LoanPaymentController::class, 'bulkDelete'])->name('loan-payments.bulk-delete');
    Route::resource('loan-payments', LoanPaymentController::class);
    Route::post('loan-payments/{loanPayment}/pay', [LoanPaymentController::class, 'pay'])->name('loan-payments.pay');

    // Withdrawal Requests
    Route::get('withdrawals', [App\Http\Controllers\WithdrawalRequestController::class, 'index'])->name('withdrawals.index');
    Route::get('withdrawals/create', [App\Http\Controllers\WithdrawalRequestController::class, 'create'])->name('withdrawals.create');
    Route::post('withdrawals', [App\Http\Controllers\WithdrawalRequestController::class, 'store'])->name('withdrawals.store');
    Route::patch('withdrawals/{withdrawal}/approve', [App\Http\Controllers\WithdrawalRequestController::class, 'approve'])->name('withdrawals.approve');
    Route::patch('withdrawals/{withdrawal}/reject', [App\Http\Controllers\WithdrawalRequestController::class, 'reject'])->name('withdrawals.reject');
    Route::patch('withdrawals/{withdrawal}/complete', [App\Http\Controllers\WithdrawalRequestController::class, 'complete'])->name('withdrawals.complete');

    // SHU (Sisa Hasil Usaha)
    Route::get('shu/tutorial', [App\Http\Controllers\ShuController::class, 'tutorial'])->name('shu.tutorial');
    Route::get('shu/simulator', [App\Http\Controllers\ShuController::class, 'simulator'])->name('shu.simulator');
    Route::get('shu/my-shu', [App\Http\Controllers\ShuController::class, 'myShu'])->name('shu.my-shu');
    Route::get('shu/print-slip/{id}', [App\Http\Controllers\ShuController::class, 'printSlip'])->name('shu.print-slip');
    Route::middleware('can:admin')->group(function() {
        Route::get('shu', [App\Http\Controllers\ShuController::class, 'index'])->name('shu.index');
        Route::get('shu/export', [App\Http\Controllers\ShuController::class, 'export'])->name('shu.export');
        Route::get('shu/calculator', [App\Http\Controllers\ShuController::class, 'calculator'])->name('shu.calculator');
        Route::get('shu/print-report', [App\Http\Controllers\ShuController::class, 'printReport'])->name('shu.print-report');
        Route::post('shu/save-settings', [App\Http\Controllers\ShuController::class, 'saveSettings'])->name('shu.save-settings');
        Route::post('shu/calculate', [App\Http\Controllers\ShuController::class, 'calculate'])->name('shu.calculate');
        Route::post('shu/distribute', [App\Http\Controllers\ShuController::class, 'distribute'])->name('shu.distribute');
        Route::post('shu/post-accounting', [App\Http\Controllers\ShuController::class, 'postToAccounting'])->name('shu.post-accounting');
    });

    // Purchasing Routes
    Route::middleware('can:admin')->group(function() {
        Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
        
        Route::get('purchases/export', [App\Http\Controllers\PurchaseController::class, 'export'])->name('purchases.export');
        Route::resource('purchases', App\Http\Controllers\PurchaseController::class);
        Route::patch('purchases/{purchase}/status', [App\Http\Controllers\PurchaseController::class, 'updateStatus'])->name('purchases.update-status');

        // Operational Expenses
        Route::get('expenses/export', [App\Http\Controllers\ExpenseController::class, 'export'])->name('expenses.export');
        Route::resource('expenses/categories', App\Http\Controllers\ExpenseCategoryController::class, ['as' => 'expenses']);
        Route::resource('expenses', App\Http\Controllers\ExpenseController::class);

        // Consignment
        Route::resource('consignment/inbounds', App\Http\Controllers\ConsignmentInboundController::class, ['as' => 'consignment']);
        Route::resource('consignment/settlements', App\Http\Controllers\ConsignmentSettlementController::class, ['as' => 'consignment']);

        // Document Generator
        Route::get('documents', [App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
        Route::get('documents/{template}/create', [App\Http\Controllers\DocumentController::class, 'create'])->name('documents.create');
        Route::post('documents/{template}/generate', [App\Http\Controllers\DocumentController::class, 'generate'])->name('documents.generate');
        Route::post('documents/{template}/notify-wa', [App\Http\Controllers\DocumentController::class, 'whatsappNotify'])->name('documents.notify-wa');
        
        // Generated Document Actions
        Route::get('documents/download/{generatedDocument}', [App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
        Route::get('documents/{generatedDocument}/edit', [App\Http\Controllers\DocumentController::class, 'edit'])->name('documents.edit');
        
        // Document Template Manager
        Route::resource('document-templates', App\Http\Controllers\DocumentTemplateController::class);

        // FITUR PERBAIKAN DATABASE (Hapus jika sudah selesai)
        Route::get('fix-database-templates', function() {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('document_templates', 'code')) {
                    \Illuminate\Support\Facades\Schema::table('document_templates', function ($table) {
                        $table->string('code')->nullable()->after('type');
                    });
                }
                
                \Illuminate\Support\Facades\Artisan::call('app:setup-document-codes');
                return "✅ Database Fixed! Kolom 'code' berhasil ditambahkan. Silakan coba menu Template Dokumen kembali.";
            } catch (\Exception $e) {
                return "❌ Error: " . $e->getMessage();
            }
        });
        Route::delete('documents/{generatedDocument}', [App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');

        // Payment Gateway Settings (Admin only)
        Route::get('settings/payment-gateway', [App\Http\Controllers\PaymentGatewayController::class, 'index'])->name('settings.payment-gateway');
        Route::post('settings/payment-gateway', [App\Http\Controllers\PaymentGatewayController::class, 'update'])->name('settings.payment-gateway.update');
        Route::post('settings/payment-gateway/test', [App\Http\Controllers\PaymentGatewayController::class, 'testConnection'])->name('settings.payment-gateway.test');
    });

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/savings', [ReportController::class, 'savings'])->name('reports.savings');
    Route::get('reports/loans', [ReportController::class, 'loans'])->name('reports.loans');
    Route::get('reports/members', [ReportController::class, 'members'])->name('reports.members');
    Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('reports/financial-health', [App\Http\Controllers\FinancialHealthController::class, 'index'])->name('reports.financial-health');

    // Accounting Reports
    Route::get('journals/tutorial', [ManualJournalController::class, 'tutorial'])->name('journals.tutorial');
    Route::resource('journals', ManualJournalController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('reports/accounting/ledger', [ReportController::class, 'generalLedger'])->name('reports.ledger');
    Route::get('reports/accounting/trial-balance', [ReportController::class, 'trialBalance'])->name('reports.trial-balance');
    Route::get('reports/accounting/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
    Route::get('reports/accounting/income-statement', [ReportController::class, 'incomeStatement'])->name('reports.income-statement');

    // Report Exports
    Route::get('reports/members/export-pdf', [ReportController::class, 'exportMembersPDF'])->name('reports.members.pdf');
    Route::get('reports/members/export-excel', [ReportController::class, 'exportMembersExcel'])->name('reports.members.excel');
    Route::get('reports/savings/export-pdf', [ReportController::class, 'exportSavingsPDF'])->name('reports.savings.pdf');
    Route::get('reports/savings/export-excel', [ReportController::class, 'exportSavingsExcel'])->name('reports.savings.excel');
    Route::get('reports/loans/export-pdf', [ReportController::class, 'exportLoansPDF'])->name('reports.loans.pdf');
    Route::get('reports/loans/export-excel', [ReportController::class, 'exportLoansExcel'])->name('reports.loans.excel');

    // Import Data (Strictly super-admin)
    Route::middleware('can:super-admin')->group(function() {
        Route::get('import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
        Route::post('import/members', [App\Http\Controllers\ImportController::class, 'importMembers'])->name('import.members');
        Route::post('import/savings', [App\Http\Controllers\ImportController::class, 'importSavings'])->name('import.savings');
        Route::post('import/loans', [App\Http\Controllers\ImportController::class, 'importLoans'])->name('import.loans');
        Route::post('import/credit-payments', [App\Http\Controllers\ImportController::class, 'importCreditPayments'])->name('import.credit_payments');
        Route::get('import/template/{type}', [App\Http\Controllers\ImportController::class, 'downloadTemplate'])->name('import.template');
        
        // Reset Data (Admin only)
        Route::post('import/reset/members', [App\Http\Controllers\ImportController::class, 'resetMembers'])->name('import.reset.members');
        Route::post('import/reset/savings', [App\Http\Controllers\ImportController::class, 'resetSavings'])->name('import.reset.savings');
        Route::post('import/reset/loans', [App\Http\Controllers\ImportController::class, 'resetLoans'])->name('import.reset.loans');
        Route::post('import/reset/purchases', [App\Http\Controllers\ImportController::class, 'resetPurchases'])->name('import.reset.purchases');
        Route::post('import/reset/transactions', [App\Http\Controllers\ImportController::class, 'resetTransactions'])->name('import.reset.transactions');
        Route::post('import/reset/all', [App\Http\Controllers\ImportController::class, 'resetAll'])->name('import.reset.all');
        
        // Generate Journals from Imported Data
        Route::post('import/generate-journals/savings', [App\Http\Controllers\ImportController::class, 'generateSavingsJournals'])->name('import.generate.savings');
        Route::post('import/generate-journals/loans', [App\Http\Controllers\ImportController::class, 'generateLoansJournals'])->name('import.generate.loans');
    });

    // Announcements
    Route::resource('announcements', AnnouncementController::class);
    

    // Information Pages (Restricted to completed profiles)
    Route::get('governance', [App\Http\Controllers\InformationController::class, 'governance'])->name('governance');
    Route::get('establishment', [App\Http\Controllers\InformationController::class, 'establishment'])->name('establishment');
    Route::post('establishment/install', [App\Http\Controllers\InformationController::class, 'installEstablishment'])->name('establishment.install');
    Route::get('documentation', [App\Http\Controllers\InformationController::class, 'documentation'])->name('documentation');
    Route::get('uat', [App\Http\Controllers\InformationController::class, 'uat'])->name('uat');

    // E-Polling
    Route::get('polls', [App\Http\Controllers\PollController::class, 'index'])->name('polls.index');
    Route::get('polls/{poll}/results', [App\Http\Controllers\PollController::class, 'results'])->name('polls.results');

    // Aspirasi Anggota (Survey)
    Route::get('aspirations', [MemberAspirationController::class, 'index'])->name('aspirations.index');
    Route::get('aspirations/create', [MemberAspirationController::class, 'create'])->name('aspirations.create');
    Route::post('aspirations', [MemberAspirationController::class, 'store'])->name('aspirations.store');

    // Member Shop (Online) - Available for all active members
    Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('shop/product/{product}', [ShopController::class, 'show'])->name('shop.show');
    Route::post('shop/product/{product}/review', [ShopController::class, 'storeReview'])->name('shop.review');
    Route::get('shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
    Route::post('shop/cart/add/{product}', [ShopController::class, 'addToCart'])->name('shop.add');
    Route::post('shop/voucher/apply', [ShopController::class, 'applyVoucher'])->name('shop.voucher.apply');
    Route::patch('shop/cart/update', [ShopController::class, 'updateCart'])->name('shop.update');
    Route::delete('shop/cart/remove', [ShopController::class, 'removeFromCart'])->name('shop.remove');
    Route::get('shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
    Route::post('shop/checkout', [ShopController::class, 'processCheckout'])->name('shop.process');
    Route::get('shop/history', [ShopController::class, 'history'])->name('shop.history');
    Route::get('shop/track/{transaction}', [ShopController::class, 'trackOrder'])->name('shop.track');

    // Administrative & Store Management (Admin & Pengurus Access)
    Route::middleware('can:admin')->group(function () {
        // Koperasi Mart Management (Accessible to Admin & Pengurus/Store Manager)
        Route::resource('categories', CategoryController::class);
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/image', [ProductController::class, 'updateImage'])->name('products.image.update');
        Route::get('products-labels/print', [ProductController::class, 'printLabels'])->name('products.print-labels');
        
        // Bulk Upload Products
        Route::get('products-bulk', [ProductController::class, 'bulkUpload'])->name('products.bulk');
        Route::get('products-bulk/template', [ProductController::class, 'downloadTemplate'])->name('products.bulk.template');
        Route::post('products-bulk/import', [ProductController::class, 'importExcel'])->name('products.bulk.import');
        Route::post('products-bulk/images', [ProductController::class, 'bulkImages'])->name('products.bulk.images');
        
        Route::resource('vouchers', App\Http\Controllers\VoucherController::class);
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos/transaction', [PosController::class, 'store'])->name('pos.store');
        Route::get('pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::get('pos/history', [PosController::class, 'history'])->name('pos.history');
        Route::get('pos/history/print', [PosController::class, 'printHistory'])->name('pos.history.print');
        Route::get('pos/history/export', [PosController::class, 'export'])->name('pos.history.export');
        
        // POS Scanning Routes
        Route::get('pos/scan', [PosController::class, 'scan'])->name('pos.scan');
        Route::post('pos/scan', [PosController::class, 'processScan'])->name('pos.scan.process');
        Route::get('pos/manage/{transaction}', [PosController::class, 'manage'])->name('pos.manage');

        Route::get('pos/receipt/{transaction}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::post('pos/orders/{transaction}/process', [PosController::class, 'processOrder'])->name('pos.orders.process');
        Route::get('pos/credits', [PosController::class, 'credits'])->name('pos.credits');
        Route::post('pos/credits/remind-all', [PosController::class, 'remindAll'])->name('pos.credits.remind-all');
        Route::post('pos/credits/{transaction}/pay', [PosController::class, 'payCredit'])->name('pos.credits.pay');

        // Poll Management (Shared Access)
        Route::get('polls/create', [App\Http\Controllers\PollController::class, 'create'])->name('polls.create');
        Route::post('polls', [App\Http\Controllers\PollController::class, 'store'])->name('polls.store');
        Route::patch('polls/{poll}/status', [App\Http\Controllers\PollController::class, 'updateStatus'])->name('polls.update-status');
        Route::delete('polls/{poll}', [App\Http\Controllers\PollController::class, 'destroy'])->name('polls.destroy');

        // Bank Reconciliation (Accounting)
        Route::group(['prefix' => 'accounting/reconciliation', 'as' => 'reconciliation.'], function () {
            Route::get('/', [App\Http\Controllers\BankReconciliationController::class, 'index'])->name('index');
            Route::get('/template', [App\Http\Controllers\BankReconciliationController::class, 'downloadTemplate'])->name('template');
            Route::post('/import', [App\Http\Controllers\BankReconciliationController::class, 'import'])->name('import');
            Route::get('/auto-match', [App\Http\Controllers\BankReconciliationController::class, 'autoMatch'])->name('auto-match');
            Route::post('/{bankTransaction}/match', [App\Http\Controllers\BankReconciliationController::class, 'match'])->name('match');
            Route::post('/{bankTransaction}/create-journal', [App\Http\Controllers\BankReconciliationController::class, 'createJournal'])->name('create-journal');
            Route::post('/{bankTransaction}/unmatch', [App\Http\Controllers\BankReconciliationController::class, 'unmatch'])->name('unmatch');
        });

        // Strictly SYSTEM ADMIN restricted modules
        Route::middleware('can:super-admin')->group(function() {
            // Master Data
            Route::get('master-data', [App\Http\Controllers\MasterDataController::class, 'index'])->name('master-data.index');
            Route::post('master-data/departments', [App\Http\Controllers\MasterDataController::class, 'storeDepartment'])->name('master-data.departments.store');
            Route::put('master-data/departments/{department}', [App\Http\Controllers\MasterDataController::class, 'updateDepartment'])->name('master-data.departments.update');
            Route::delete('master-data/departments/{department}', [App\Http\Controllers\MasterDataController::class, 'destroyDepartment'])->name('master-data.departments.destroy');
            Route::post('master-data/positions', [App\Http\Controllers\MasterDataController::class, 'storePosition'])->name('master-data.positions.store');
            Route::put('master-data/positions/{position}', [App\Http\Controllers\MasterDataController::class, 'updatePosition'])->name('master-data.positions.update');
            Route::delete('master-data/positions/{position}', [App\Http\Controllers\MasterDataController::class, 'destroyPosition'])->name('master-data.positions.destroy');

            // Roles & Permissions
            Route::get('roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
            Route::get('roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
            Route::put('roles/user/{user}', [App\Http\Controllers\RoleController::class, 'updateRole'])->name('roles.update-user');
            Route::get('roles/{role}/permissions', [App\Http\Controllers\RoleController::class, 'getPermissions'])->name('roles.permissions');

            // System Settings
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
            Route::post('settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');
            Route::get('settings/sync-prices/preview', [SettingController::class, 'previewPrices'])->name('settings.sync-prices.preview');
            Route::post('settings/sync-prices', [SettingController::class, 'recalculatePrices'])->name('settings.sync-prices');
            
            // Landing Page Settings
            Route::get('settings/landing', [LandingSettingController::class, 'index'])->name('settings.landing');
            Route::put('settings/landing', [LandingSettingController::class, 'update'])->name('settings.landing.update');
            Route::post('settings/landing/member', [LandingSettingController::class, 'storeMember'])->name('settings.landing.member.store');
            Route::delete('settings/landing/member/{id}', [LandingSettingController::class, 'destroyMember'])->name('settings.landing.member.destroy');
            Route::post('settings/landing/program', [LandingSettingController::class, 'storeProgram'])->name('settings.landing.program.store');
            Route::delete('settings/landing/program/{id}', [LandingSettingController::class, 'destroyProgram'])->name('settings.landing.program.destroy');
            
            // Backup & Restore
            Route::get('settings/backup', [App\Http\Controllers\BackupController::class, 'index'])->name('settings.backup');
            Route::get('settings/backup/download', [App\Http\Controllers\BackupController::class, 'download'])->name('settings.backup.download');
            Route::post('settings/backup/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('settings.backup.restore');
            Route::delete('settings/backup/{filename}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('settings.backup.destroy');
            Route::post('settings/backup/reset', [App\Http\Controllers\BackupController::class, 'reset'])->name('settings.backup.reset');
            
            // Audit Logs
            Route::get('settings/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('settings.audit-logs');
            
            // AI & Integrations Settings
            Route::get('settings/ai', [App\Http\Controllers\AiSettingController::class, 'index'])->name('settings.ai');
            Route::post('settings/ai', [App\Http\Controllers\AiSettingController::class, 'update'])->name('settings.ai.update');
            Route::post('settings/ai/test', [App\Http\Controllers\AiSettingController::class, 'testConnection'])->name('settings.ai.test');
            Route::post('settings/ai/test-fonnte', [App\Http\Controllers\AiSettingController::class, 'testFonnte'])->name('settings.ai.test-fonnte');
            Route::post('ai/chat', [App\Http\Controllers\AiSettingController::class, 'chat'])->name('ai.chat');
        });
    });

    // Member voting (show must stay below create to avoid conflict)
    Route::get('polls/{poll}', [App\Http\Controllers\PollController::class, 'show'])->name('polls.show');
    Route::post('polls/{poll}/vote', [App\Http\Controllers\PollController::class, 'vote'])->name('polls.vote');
});

require __DIR__.'/auth.php';
