<?php

namespace Database\Seeders;

use App\Models\LegalCase;
use App\Models\CaseParty;
use App\Models\CaseDocument;
use App\Models\CaseDeadline;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we already have case management data
        if (LegalCase::count() > 0) {
            $this->command->info('Case management data already exists. Skipping seeder.');
            return;
        }

        $this->command->info('Creating case management test data...');

        // Create some test users if they don't exist
        $legalProfessional = User::firstOrCreate(
            ['email' => 'paralegal@legal.com'],
            [
                'name' => 'Sarah Johnson',
                'password' => bcrypt('password'),
                'user_type' => 'legal_professional',
                'email_verified_at' => now(),
            ]
        );

        $proSeUser = User::firstOrCreate(
            ['email' => 'johnsmith@email.com'],
            [
                'name' => 'John Smith',
                'password' => bcrypt('password'),
                'user_type' => 'pro_se',
                'email_verified_at' => now(),
            ]
        );

        // Create cases for the legal professional (complex cases)
        $this->createLegalProfessionalCases($legalProfessional);

        // Create cases for the pro-se user (simpler cases)
        $this->createProSeCases($proSeUser);

        $this->command->info('Case management test data created successfully!');
    }

    /**
     * Create complex cases for legal professionals.
     */
    private function createLegalProfessionalCases(User $user): void
    {
        // Create 3-5 cases for the legal professional
        $cases = LegalCase::factory()
            ->count(4)
            ->forUser($user)
            ->create([
                'status' => 'active'
            ]);

        foreach ($cases as $case) {
            // Add parties to each case (more complex party structure)
            $this->createPartiesForCase($case, true);
            
            // Add documents to each case
            $this->createDocumentsForCase($case, true);
            
            // Add deadlines to each case
            $this->createDeadlinesForCase($case, true);
        }

        // Create one closed case
        $closedCase = LegalCase::factory()
            ->forUser($user)
            ->closed()
            ->create();

        $this->createPartiesForCase($closedCase, true);
        $this->createDocumentsForCase($closedCase, true, 'closed');
        $this->createDeadlinesForCase($closedCase, true, 'closed');
    }

    /**
     * Create simpler cases for pro-se users.
     */
    private function createProSeCases(User $user): void
    {
        // Create 1-2 cases for the pro-se user
        $cases = LegalCase::factory()
            ->count(2)
            ->forUser($user)
            ->create([
                'type' => 'landlord_tenant', // Simpler case type
                'status' => 'active'
            ]);

        foreach ($cases as $case) {
            // Add parties to each case (simpler party structure)
            $this->createPartiesForCase($case, false);
            
            // Add documents to each case
            $this->createDocumentsForCase($case, false);
            
            // Add deadlines to each case
            $this->createDeadlinesForCase($case, false);
        }
    }

    /**
     * Create parties for a case.
     */
    private function createPartiesForCase(LegalCase $case, bool $isComplex = true): void
    {
        if ($isComplex) {
            // Legal professional cases - more parties
            CaseParty::factory()->plaintiff()->forCase($case)->create();
            CaseParty::factory()->defendant()->forCase($case)->create();
            CaseParty::factory()->attorney()->forCase($case)->create();
            CaseParty::factory()->judge()->forCase($case)->create();
            
            // Sometimes add additional parties
            if (rand(1, 100) > 50) {
                CaseParty::factory()->defendant()->organization()->forCase($case)->create();
            }
            if (rand(1, 100) > 70) {
                CaseParty::factory()->attorney()->forCase($case)->create(); // Defense attorney
            }
        } else {
            // Pro-se cases - fewer parties
            CaseParty::factory()->plaintiff()->individual()->forCase($case)->create();
            CaseParty::factory()->defendant()->forCase($case)->create();
            
            // Sometimes add a judge
            if (rand(1, 100) > 60) {
                CaseParty::factory()->judge()->forCase($case)->create();
            }
        }
    }

    /**
     * Create documents for a case.
     */
    private function createDocumentsForCase(LegalCase $case, bool $isComplex = true, string $caseStatus = 'active'): void
    {
        if ($isComplex) {
            // Legal professional cases - more documents
            CaseDocument::factory()->forCase($case)->ofCategory('complaint')->processed()->create();
            CaseDocument::factory()->forCase($case)->ofCategory('motion')->processed()->create();
            CaseDocument::factory()->forCase($case)->ofCategory('evidence')->pending()->create();
            CaseDocument::factory()->forCase($case)->ofCategory('discovery')->processed()->create();
            
            // Add more documents based on case status
            if ($caseStatus === 'closed') {
                CaseDocument::factory()->forCase($case)->ofCategory('order')->processed()->create();
                CaseDocument::factory()->count(2)->forCase($case)->processed()->create();
            } else {
                CaseDocument::factory()->count(2)->forCase($case)->create();
            }
        } else {
            // Pro-se cases - fewer documents
            CaseDocument::factory()->forCase($case)->ofCategory('complaint')->create();
            CaseDocument::factory()->forCase($case)->ofCategory('evidence')->create();
            
            if ($caseStatus === 'active') {
                CaseDocument::factory()->forCase($case)->pending()->create();
            }
        }
    }

    /**
     * Create deadlines for a case.
     */
    private function createDeadlinesForCase(LegalCase $case, bool $isComplex = true, string $caseStatus = 'active'): void
    {
        if ($isComplex) {
            // Legal professional cases - more deadlines
            if ($caseStatus === 'active') {
                // Future deadlines
                CaseDeadline::factory()->forCase($case)->courtFiling()->dueSoon()->create();
                CaseDeadline::factory()->forCase($case)->discovery()->future()->create();
                CaseDeadline::factory()->forCase($case)->future()->create();
                
                // Some completed deadlines
                CaseDeadline::factory()->forCase($case)->completed()->create();
                CaseDeadline::factory()->forCase($case)->completed()->create();
                
                // Maybe one overdue
                if (rand(1, 100) > 70) {
                    CaseDeadline::factory()->forCase($case)->overdue()->create();
                }
            } else {
                // Closed case - mostly completed deadlines
                CaseDeadline::factory()->count(5)->forCase($case)->completed()->create();
                CaseDeadline::factory()->forCase($case)->completed()->create();
            }
        } else {
            // Pro-se cases - fewer, simpler deadlines
            if ($caseStatus === 'active') {
                CaseDeadline::factory()->forCase($case)->dueSoon()->create();
                CaseDeadline::factory()->forCase($case)->future()->create();
                
                // Maybe one completed
                if (rand(1, 100) > 50) {
                    CaseDeadline::factory()->forCase($case)->completed()->create();
                }
            }
        }
    }
}
