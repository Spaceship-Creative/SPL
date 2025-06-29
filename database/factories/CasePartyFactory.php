<?php

namespace Database\Factories;

use App\Models\CaseParty;
use App\Models\LegalCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseParty>
 */
class CasePartyFactory extends Factory
{
    protected $model = CaseParty::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['plaintiff', 'defendant', 'attorney', 'judge', 'witness'];
        $partyTypes = ['individual', 'organization', 'court_official'];
        
        $role = $this->faker->randomElement($roles);
        $partyType = $this->getPartyTypeForRole($role);

        return [
            'case_id' => LegalCase::factory(),
            'name' => $this->generateNameForRole($role, $partyType),
            'role' => $role,
            'party_type' => $partyType,
            'contact_info' => $this->generateContactInfo(),
            'address' => $this->faker->optional(0.7)->address,
            'phone' => $this->faker->optional(0.8)->phoneNumber,
            'email' => $this->faker->optional(0.6)->safeEmail,
        ];
    }

    /**
     * Get appropriate party type based on role.
     */
    private function getPartyTypeForRole(string $role): string
    {
        return match($role) {
            'judge' => 'court_official',
            'attorney' => $this->faker->randomElement(['individual', 'organization']),
            'plaintiff', 'defendant' => $this->faker->randomElement(['individual', 'organization']),
            'witness' => 'individual',
            default => 'individual',
        };
    }

    /**
     * Generate a realistic name based on role and party type.
     */
    private function generateNameForRole(string $role, string $partyType): string
    {
        if ($partyType === 'organization') {
            $companyNames = [
                'ABC Corporation',
                'Pacific Construction LLC',
                'Golden Gate Properties',
                'Metro Healthcare Group',
                'Westfield Development Co.',
                'Sunrise Technologies Inc.',
                'Bay Area Legal Services',
                'Central Insurance Agency',
                'Riverside Manufacturing',
                'Downtown Retail Partners'
            ];
            return $this->faker->randomElement($companyNames);
        }

        if ($role === 'judge') {
            return 'The Honorable ' . $this->faker->name();
        }

        if ($role === 'attorney') {
            return $this->faker->name() . ', Esq.';
        }

        return $this->faker->name();
    }

    /**
     * Generate contact info JSON.
     */
    private function generateContactInfo(): ?array
    {
        if ($this->faker->boolean(0.6)) {
            return [
                'preferred_contact' => $this->faker->randomElement(['email', 'phone', 'mail']),
                'best_time_to_call' => $this->faker->optional()->randomElement(['morning', 'afternoon', 'evening']),
                'notes' => $this->faker->optional()->sentence(),
            ];
        }

        return null;
    }

    /**
     * Indicate that the party is a plaintiff.
     */
    public function plaintiff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'plaintiff',
            'party_type' => $this->faker->randomElement(['individual', 'organization']),
        ]);
    }

    /**
     * Indicate that the party is a defendant.
     */
    public function defendant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'defendant',
            'party_type' => $this->faker->randomElement(['individual', 'organization']),
        ]);
    }

    /**
     * Indicate that the party is an attorney.
     */
    public function attorney(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'attorney',
            'party_type' => 'individual',
            'name' => $this->faker->name() . ', Esq.',
        ]);
    }

    /**
     * Indicate that the party is a judge.
     */
    public function judge(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'judge',
            'party_type' => 'court_official',
            'name' => 'The Honorable ' . $this->faker->name(),
        ]);
    }

    /**
     * Indicate that the party is an individual.
     */
    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'party_type' => 'individual',
        ]);
    }

    /**
     * Indicate that the party is an organization.
     */
    public function organization(): static
    {
        return $this->state(fn (array $attributes) => [
            'party_type' => 'organization',
        ]);
    }

    /**
     * Indicate that the party belongs to a specific case.
     */
    public function forCase(LegalCase $case): static
    {
        return $this->state(fn (array $attributes) => [
            'case_id' => $case->id,
        ]);
    }
}
