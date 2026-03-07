<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define Polymorphic Map
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'member' => \App\Models\Member::class,
            'supplier' => \App\Models\Supplier::class,
            'saving' => \App\Models\Saving::class, // Good habit
            'consignment_settlement' => \App\Models\ConsignmentSettlement::class,
        ]);

        // Define Gates for authorization
        \Illuminate\Support\Facades\Gate::define('admin', function ($user) {
            return $user->hasAdminAccess();
        });

        \Illuminate\Support\Facades\Gate::define('store', function ($user) {
            return $user->hasStoreAccess();
        });
        
        \Illuminate\Support\Facades\Gate::define('member', function ($user) {
            return $user->isMember();
        });

        \Illuminate\Support\Facades\Gate::define('super-admin', function ($user) {
            return $user->isAdmin();
        });

        // Centralized gate for data deletion
        \Illuminate\Support\Facades\Gate::define('delete-data', function ($user) {
            return $user->isAdmin();
        });

        // Share settings to all views & Override Mail Config
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $globalSettings = \App\Models\Setting::all()->pluck('value', 'key');
            \Illuminate\Support\Facades\View::share('globalSettings', $globalSettings);

            // Dynamically override mail config if exists
            if (isset($globalSettings['mail_host'])) {
                config([
                    'mail.default' => $globalSettings['mail_mailer'] ?? config('mail.default'),
                    'mail.mailers.smtp.host' => $globalSettings['mail_host'] ?? config('mail.mailers.smtp.host'),
                    'mail.mailers.smtp.port' => $globalSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
                    'mail.mailers.smtp.encryption' => $globalSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                    'mail.mailers.smtp.username' => $globalSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                    'mail.mailers.smtp.password' => $globalSettings['mail_password'] ?? config('mail.mailers.smtp.password'),
                    'mail.from.address' => $globalSettings['mail_from_address'] ?? config('mail.from.address'),
                    'mail.from.name' => $globalSettings['mail_from_name'] ?? config('mail.from.name'),
                ]);
            }
        }

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Reset Password Akun')
                ->greeting('Halo ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan reset password untuk akun Anda.')
                ->action('Reset Password', $url)
                ->line('Link ini berlaku selama 60 menit.')
                ->line('Jika Anda tidak merasa meminta reset password, abaikan email ini.');
        });
    }
}
