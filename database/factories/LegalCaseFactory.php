<?php

namespace Database\Factories;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegalCase>
 */
class LegalCaseFactory extends Factory
{
    protected $model = LegalCase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $caseTypes = ['civil', 'criminal', 'family', 'employment', 'personal_injury', 'contract', 'landlord_tenant'];
        $statuses = ['active', 'closed', 'pending', 'archived'];
        $jurisdictions = [
            'Federal District Court - Northern District of California',
            'Superior Court of California - San Francisco County',
            'Superior Court of California - Los Angeles County',
            'Superior Court of California - San Diego County',
            'Federal District Court - Southern District of New York',
            'Superior Court of New York - Manhattan County',
            'Circuit Court of Cook County - Illinois',
            'Federal District Court - District of Columbia'
        ];
        
        $venues = [
            'San Francisco Superior Court',
            'Los Angeles Superior Court',
            'Federal Courthouse - San Francisco',
            'Manhattan Supreme Court',
            'Daley Center - Chicago',
            'U.S. District Court - Washington D.C.'
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->generateCaseName(),
            'case_number' => $this->faker->optional(0.8)->numerify('####-CV-#####'),
            'type' => $this->faker->randomElement($caseTypes),
            'jurisdiction' => $this->faker->randomElement($jurisdictions),
            'venue' => $this->faker->randomElement($venues),
            'status' => $this->faker->randomElement($statuses),
            'description' => $this->faker->optional(0.7)->paragraphs(2, true),
        ];
    }

    /**
     * Generate a realistic case name.
     */
    private function generateCaseName(): string
    {
        $caseNames = [
            'Smith v. Jones Construction Co.',
            'People v. Anderson',
            'Johnson v. City of San Francisco',
            'Williams v. ABC Corporation',
            'Brown v. State of California',
            'Davis v. Metropolitan Insurance',
            'Miller v. Westfield Properties',
            'Wilson v. Tech Solutions Inc.',
            'Moore v. Healthcare Partners',
            'Taylor v. Golden Gate Transit',
            'Thompson v. Pacific Bank',
            'Garcia v. Riverside Development',
            'Martinez v. Central Hospital',
            'Robinson v. Acme Manufacturing',
            'Clark v. Downtown Apartments'
        ];

        return $this->faker->randomElement($caseNames);
    }

    /**
     * Indicate that the case is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the case is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * Indicate that the case is civil.
     */
    public function civil(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'civil',
        ]);
    }

    /**
     * Indicate that the case is criminal.
     */
    public function criminal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'criminal',
        ]);
    }

    /**
     * Indicate that the case has a case number.
     */
    public function withCaseNumber(): static
    {
        return $this->state(fn (array $attributes) => [
            'case_number' => $this->faker->numerify('####-CV-#####'),
        ]);
    }

    /**
     * Indicate that the case belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
