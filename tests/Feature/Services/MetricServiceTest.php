<?php

namespace Tests\Feature\Services;

use App\Constants\SubscriptionStatus;
use App\Constants\TransactionStatus;
use App\Models\Currency;
use App\Models\Interval;
use App\Models\PaymentProvider;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Support\Str;
use Tests\Feature\FeatureTest;

class MetricServiceTest extends FeatureTest
{
    public function test_calculate_daily_revenue()
    {
        Transaction::query()->update(['status' => TransactionStatus::FAILED->value]);

        $user = $this->createUser();

        Transaction::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'amount' => 1000,
            'currency_id' => Currency::where('code', 'USD')->firstOrFail()->id,
            'status' => TransactionStatus::SUCCESS,
            'payment_provider_id' => PaymentProvider::firstOrFail()->id,
            'payment_provider_status' => 'success',
            'payment_provider_transaction_id' => '234',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'amount' => 1000,
            'currency_id' => Currency::where('code', 'USD')->firstOrFail()->id,
            'status' => TransactionStatus::SUCCESS,
            'payment_provider_id' => PaymentProvider::firstOrFail()->id,
            'payment_provider_status' => 'success',
            'payment_provider_transaction_id' => '234',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'uuid' => Str::uuid(),
            'amount' => 1000,
            'currency_id' => Currency::where('code', 'USD')->firstOrFail()->id,
            'status' => TransactionStatus::REFUNDED,
            'payment_provider_id' => PaymentProvider::firstOrFail()->id,
            'payment_provider_status' => 'success',
            'payment_provider_transaction_id' => '234',
        ]);

        $metricService = resolve(MetricsService::class);
        $result = $metricService->calculateDailyRevenue(now());

        $this->assertEquals($result, 10.00);
    }

    public function test_average_revenue_per_user()
    {
        Transaction::query()->update(['status' => TransactionStatus::FAILED->value]);

        $weekAgo = now()->subWeek()->endOfDay();

        $user1 = User::factory()->create([
            'created_at' => $weekAgo,
        ]);

        $user2 = User::factory()->create([
            'created_at' => $weekAgo,
        ]);

        $transaction = Transaction::create([
            'user_id' => $user1->id,
            'uuid' => Str::uuid(),
            'amount' => 1000,
            'currency_id' => Currency::where('code', 'USD')->firstOrFail()->id,
            'status' => TransactionStatus::SUCCESS->value,
            'payment_provider_id' => PaymentProvider::firstOrFail()->id,
            'payment_provider_status' => 'success',
            'payment_provider_transaction_id' => '223434',
        ]);

        $transaction->created_at = $weekAgo;
        $transaction->save();

        $transaction = Transaction::create([
            'user_id' => $user2->id,
            'uuid' => Str::uuid(),
            'amount' => 1000,
            'currency_id' => Currency::where('code', 'USD')->firstOrFail()->id,
            'status' => TransactionStatus::SUCCESS->value,
            'payment_provider_id' => PaymentProvider::firstOrFail()->id,
            'payment_provider_status' => 'success',
            'payment_provider_transaction_id' => '34555',
        ]);

        $transaction->created_at = $weekAgo;
        $transaction->save();

        $metricService = resolve(MetricsService::class);
        $result = $metricService->calculateAverageRevenuePerUser($weekAgo);

        $this->assertEquals($result, 10.00);
    }

    public function test_mrr()
    {
        Transaction::query()->update(['status' => TransactionStatus::FAILED->value]);
        Subscription::query()->update(['status' => SubscriptionStatus::NEW->value]);

        $user = $this->createUser();

        $slug = Str::random();
        $plan = Plan::factory()->create([
            'slug' => $slug,
            'is_active' => true,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::ACTIVE,
            'plan_id' => $plan->id,
            'price' => 5000,
            'interval_id' => Interval::where('slug', 'month')->firstOrFail()->id,
        ])->save();

        $metricService = resolve(MetricsService::class);
        $result = $metricService->calculateMRR(now());

        $this->assertEquals($result, 50.00);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => SubscriptionStatus::ACTIVE,
            'plan_id' => $plan->id,
            'price' => 12000,
            'interval_id' => Interval::where('slug', 'year')->firstOrFail()->id,
        ])->save();

        $result = $metricService->calculateMRR(now());

        $this->assertEquals($result, 60.00);

    }
}
