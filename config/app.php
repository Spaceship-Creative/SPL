<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'SaaSykit'),

    'description' => env('APP_DESCRIPTION', ''),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],

    'datetime_format' => 'd/m/Y H:i',
    'date_format' => 'd/m/Y',

    'default_currency' => 'USD',

    'support_email' => 'support@saasykit.com',

    'email_color_tint' => '#6f27e5',  // used to add brand color to email templates (since it's not possible to use tailwind vars in emails)

    'google_tracking_id' => env('GOOGLE_TRACKING_ID'),
    'tracking_scripts' => env('TRACKING_SCRIPTS'),

    'payment' => [
        'proration_enabled' => env('PAYMENT_PRORATION_ENABLED', true),
    ],

    'customer_dashboard' => [
        'show_subscriptions' => true,
        'show_orders' => true,
        'show_transactions' => true,
    ],

    'social_links' => [
        'facebook' => env('SOCIAL_FACEBOOK_URL'),
        'x' => env('SOCIAL_X_URL'),
        'linkedin' => env('SOCIAL_LINKEDIN_URL'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL'),
        'youtube' => env('SOCIAL_YOUTUBE_URL'),
        'github' => env('SOCIAL_GITHUB_URL'),
        'discord' => env('SOCIAL_DISCORD_URL'),
    ],

    // this is the name of the logo file in the public directory
    'logo' => [
        'light' => 'images/logo-light.png',
        'dark' => 'images/logo-dark.png',
    ],

    'roadmap_enabled' => env('ROADMAP_ENABLED', true),

    'recaptcha_enabled' => env('RECAPTCHA_ENABLED', false),

    'multiple_subscriptions_enabled' => env('MULTIPLE_SUBSCRIPTIONS_ENABLED', false),

    'two_factor_auth_enabled' => env('TWO_FACTOR_AUTH_ENABLED', true),

    'trial_without_payment' => [
        'enabled' => env('TRIAL_WITHOUT_PAYMENT_ENABLED', false),
        'first_reminder_enabled' => env('TRIAL_WITHOUT_PAYMENT_FIRST_REMINDER_ENABLED', true),
        'first_reminder_days' => env('TRIAL_WITHOUT_PAYMENT_FIRST_REMINDER_DAYS', 3),
        'second_reminder_enabled' => env('TRIAL_WITHOUT_PAYMENT_SECOND_REMINDER_ENABLED', true),
        'second_reminder_days' => env('TRIAL_WITHOUT_PAYMENT_SECOND_REMINDER_DAYS', 1),
        'sms_verification_enabled' => env('TRIAL_WITHOUT_PAYMENT_SMS_VERIFICATION_ENABLED', false),
    ],

    'limit_user_trials' => [
        'enabled' => env('LIMIT_USER_TRIALS_ENABLED', false),
        'max_count' => env('LIMIT_USER_TRIALS_MAX_COUNT', 1),
    ],

    'verification' => [
        'default_provider' => env('VERIFICATION_DEFAULT_PROVIDER', 'twilio'),
    ],

    'admin_settings' => [
        'enabled' => env('ADMIN_SETTINGS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
    ])->toArray(),

];
