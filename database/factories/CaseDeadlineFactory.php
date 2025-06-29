<?php

namespace Database\Factories;

use App\Models\CaseDeadline;
use App\Models\LegalCase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseDeadline>
 */
class CaseDeadlineFactory extends Factory
{
    protected $model = CaseDeadline::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'completed', 'overdue', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        
        $dueDate = $this->faker->dateTimeBetween('-60 days', '+120 days');
        $reminderDate = $this->faker->optional(0.8)->dateTimeBetween(
            Carbon::parse($dueDate)->subDays(14),
            Carbon::parse($dueDate)->subDay()
        );

        return [
            'case_id' => LegalCase::factory(),
            'title' => $this->generateDeadlineTitle(),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'due_date' => $dueDate,
            'reminder_date' => $reminderDate,
            'status' => $this->faker->randomElement($statuses),
            'priority' => $this->faker->randomElement($priorities),
        ];
    }

    /**
     * Generate a realistic deadline title.
     */
    private function generateDeadlineTitle(): string
    {
        $deadlineTitles = [
            'File Answer to Complaint',
            'Discovery Deadline',
            'Motion to Dismiss Due',
            'Summary Judgment Brief Due',
            'Deposition of Expert Witness',
            'Settlement Conference',
            'Pre-Trial Conference',
            'Trial Date',
            'Appeal Filing Deadline',
            'Request for Production Response',
            'Interrogatory Responses Due',
            'Expert Witness Disclosure',
            'Mediation Session',
            'Status Conference',
            'Motion Hearing',
            'Final Pretrial Statements Due',
            'Witness List Submission',
            'Exhibit List Filing',
            'Opening Brief Due',
            'Reply Brief Deadline',
            'Oral Argument Date',
            'Compliance Deadline',
            'Document Production Due',
            'Client Meeting - Case Review',
            'Settlement Offer Response Deadline'
        ];

        return $this->faker->randomElement($deadlineTitles);
    }

    /**
     * Indicate that the deadline is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the deadline is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the deadline is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the deadline is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the deadline is critical priority.
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'critical',
        ]);
    }

    /**
     * Indicate that the deadline is due soon (within next 7 days).
     */
    public function dueSoon(): static
    {
        $dueDate = $this->faker->dateTimeBetween('now', '+7 days');
        $reminderDate = Carbon::parse($dueDate)->subDay();
        
        return $this->state(fn (array $attributes) => [
            'due_date' => $dueDate,
            'reminder_date' => $reminderDate > now() ? $this->faker->dateTimeBetween('now', $reminderDate) : now(),
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the deadline is due today.
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $this->faker->dateTimeBetween('today', 'today 23:59:59'),
            'reminder_date' => $this->faker->dateTimeBetween('-3 days', 'now'),
            'status' => 'pending',
            'priority' => $this->faker->randomElement(['high', 'critical']),
        ]);
    }

    /**
     * Indicate that the deadline is far in the future.
     */
    public function future(): static
    {
        $dueDate = $this->faker->dateTimeBetween('+30 days', '+180 days');
        return $this->state(fn (array $attributes) => [
            'due_date' => $dueDate,
            'reminder_date' => $this->faker->optional(0.9)->dateTimeBetween(
                Carbon::parse($dueDate)->subDays(21),
                Carbon::parse($dueDate)->subDays(7)
            ),
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the deadline has a reminder set.
     */
    public function withReminder(): static
    {
        return $this->state(function (array $attributes) {
            $dueDate = $attributes['due_date'] ?? $this->faker->dateTimeBetween('now', '+30 days');
            $startDate = Carbon::parse($dueDate)->subDays(14);
            $endDate = Carbon::parse($dueDate)->subDay();
            
            // Ensure start date is not after end date
            if ($startDate > $endDate) {
                $startDate = $endDate;
            }
            
            return [
                'reminder_date' => $this->faker->dateTimeBetween($startDate, $endDate),
            ];
        });
    }

    /**
     * Indicate that the deadline belongs to a specific case.
     */
    public function forCase(LegalCase $case): static
    {
        return $this->state(fn (array $attributes) => [
            'case_id' => $case->id,
        ]);
    }

    /**
     * Create a court filing deadline.
     */
    public function courtFiling(): static
    {
        $filingTitles = [
            'File Answer to Complaint',
            'Motion to Dismiss Due',
            'Summary Judgment Brief Due',
            'Appeal Filing Deadline',
            'Opening Brief Due',
            'Reply Brief Deadline'
        ];

        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->randomElement($filingTitles),
            'priority' => $this->faker->randomElement(['high', 'critical']),
        ]);
    }

    /**
     * Create a discovery deadline.
     */
    public function discovery(): static
    {
        $discoveryTitles = [
            'Discovery Deadline',
            'Request for Production Response',
            'Interrogatory Responses Due',
            'Expert Witness Disclosure',
            'Document Production Due'
        ];

        return $this->state(fn (array $attributes) => [
            'title' => $this->faker->randomElement($discoveryTitles),
            'priority' => $this->faker->randomElement(['medium', 'high']),
        ]);
    }
}
