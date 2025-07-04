<?php

namespace App\Models;

use App\Notifications\Auth\QueuedVerifyEmail;
use App\Services\OrderService;
use App\Services\SubscriptionService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laragear\TwoFactor\Contracts\TwoFactorAuthenticatable;
use Laragear\TwoFactor\TwoFactorAuthentication;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, TwoFactorAuthenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, TwoFactorAuthentication;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'is_admin',
        'public_name',
        'is_blocked',
        'notes',
        'phone_number',
        'phone_number_verified_at',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
    ];

    public function roadmapItems()
    {
        return $this->hasMany(RoadmapItem::class);
    }

    public function roadmapItemUpvotes()
    {
        return $this->belongsToMany(RoadmapItem::class, 'roadmap_item_user_upvotes');
    }

    public function userParameters(): HasMany
    {
        return $this->hasMany(UserParameter::class);
    }

    public function stripeData(): HasMany
    {
        return $this->hasMany(UserStripeData::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionTrials(): HasMany
    {
        return $this->hasMany(UserSubscriptionTrial::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() == 'admin' && ! $this->is_admin) {
            return false;
        }

        return true;
    }

    public function getPublicName()
    {
        return $this->public_name ?? $this->name;
    }

    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function isPhoneNumberVerified()
    {
        return $this->phone_number_verified_at !== null;
    }

    public function canImpersonate()
    {
        return $this->hasPermissionTo('impersonate users') && $this->isAdmin();
    }

    public function isSubscribed(?string $productSlug = null): bool
    {
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);

        return $subscriptionService->isUserSubscribed($this, $productSlug);
    }

    public function isTrialing(?string $productSlug = null): bool
    {
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);

        return $subscriptionService->isUserTrialing($this, $productSlug);
    }

    public function hasPurchased(?string $productSlug = null): bool
    {
        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);

        return $orderService->hasUserOrdered($this, $productSlug);
    }

    public function subscriptionProductMetadata()
    {
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app(SubscriptionService::class);

        return $subscriptionService->getUserSubscriptionProductMetadata($this);

    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueuedVerifyEmail);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Get the legal cases for the user.
     */
    public function legalCases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }

    /**
     * Check if user is a legal professional
     */
    public function isLegalProfessional(): bool
    {
        return $this->user_type === 'legal_professional';
    }

    /**
     * Check if user is a pro-se litigant
     */
    public function isProSe(): bool
    {
        return $this->user_type === 'pro_se';
    }

    /**
     * Get user type display name
     */
    public function getUserTypeDisplayAttribute(): string
    {
        return match($this->user_type) {
            'legal_professional' => 'Legal Professional',
            'pro_se' => 'Pro-Se Litigant',
            default => 'Unknown'
        };
    }
}
