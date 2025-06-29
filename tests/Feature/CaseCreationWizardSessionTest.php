<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\CaseCreationWizard;

class CaseCreationWizardSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->legalProfessional = User::factory()->create([
            'user_type' => 'legal_professional',
            'email' => 'legal@test.com'
        ]);
        
        $this->proSeUser = User::factory()->create([
            'user_type' => 'pro_se',
            'email' => 'prose@test.com'
        ]);
    }

    /** @test */
    public function wizard_persists_current_step_in_session()
    {
        $this->actingAs($this->proSeUser);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Start at step 1
        $component->assertSet('currentStep', 1);
        
        // Set required basic information to pass validation
        $basicInfo = [
            'name' => 'Test Case',
            'case_number' => '',
            'type' => 'civil',
            'jurisdiction' => 'State',
            'venue' => 'District Court',
            'description' => 'This is a test case description for validation'
        ];
        
        $component->set('caseData', array_merge($component->get('caseData'), $basicInfo));
        
        // Move to step 2
        $component->call('nextStep');
        $component->assertSet('currentStep', 2);
        
        // Verify session has the step
        $this->assertTrue(session()->has('wizard_current_step'));
        $this->assertEquals(2, session('wizard_current_step'));
        
        // Create new component instance (simulating page reload)
        $newComponent = Livewire::test(CaseCreationWizard::class);
        
        // Should restore from session
        $newComponent->assertSet('currentStep', 2);
    }

    /** @test */
    public function wizard_persists_basic_info_in_session()
    {
        $this->actingAs($this->legalProfessional);

        $basicInfo = [
            'name' => 'Test Case vs. Defendant',
            'case_number' => 'CV-2024-001',
            'type' => 'civil',
            'jurisdiction' => 'Federal',
            'venue' => 'District Court',
            'description' => 'This is a test case description'
        ];

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set basic info
        $component->set('basicInfo', $basicInfo);
        
        // Verify session has the data
        $this->assertTrue(session()->has('wizard_basic_info'));
        $this->assertEquals($basicInfo, session('wizard_basic_info'));
        
        // Create new component instance
        $newComponent = Livewire::test(CaseCreationWizard::class);
        
        // Should restore basic info from session
        $newComponent->assertSet('basicInfo', $basicInfo);
    }

    /** @test */
    public function wizard_persists_parties_in_session()
    {
        $this->actingAs($this->proSeUser);

        $parties = [
            [
                'id' => 'party-1',
                'name' => 'John Plaintiff',
                'type' => 'plaintiff',
                'category' => 'individual',
                'email' => 'john@test.com',
                'phone' => '555-0123',
                'address' => '123 Main St'
            ],
            [
                'id' => 'party-2',
                'name' => 'Jane Defendant',
                'type' => 'defendant',
                'category' => 'individual',
                'email' => 'jane@test.com',
                'phone' => '555-0456',
                'address' => '456 Oak Ave'
            ]
        ];

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set parties
        $component->set('parties', $parties);
        
        // Verify session has the data
        $this->assertTrue(session()->has('wizard_parties'));
        $this->assertEquals($parties, session('wizard_parties'));
        
        // Create new component instance
        $newComponent = Livewire::test(CaseCreationWizard::class);
        
        // Should restore parties from session
        $newComponent->assertSet('parties', $parties);
    }

    /** @test */
    public function wizard_persists_key_dates_in_session()
    {
        $this->actingAs($this->proSeUser);

        $keyDates = [
            [
                'id' => 'date-1',
                'title' => 'Response Deadline',
                'date' => '2024-12-31',
                'time' => '17:00',
                'priority' => 'high',
                'type' => 'deadline',
                'description' => 'Deadline to respond to complaint'
            ]
        ];

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set key dates
        $component->set('keyDates', $keyDates);
        
        // Verify session has the data
        $this->assertTrue(session()->has('wizard_key_dates'));
        $this->assertEquals($keyDates, session('wizard_key_dates'));
        
        // Create new component instance
        $newComponent = Livewire::test(CaseCreationWizard::class);
        
        // Should restore key dates from session
        $newComponent->assertSet('keyDates', $keyDates);
    }

    /** @test */
    public function wizard_persists_documents_in_session()
    {
        $this->actingAs($this->legalProfessional);

        $documents = [
            [
                'id' => 'doc-1',
                'title' => 'Original Complaint',
                'category' => 'complaint',
                'description' => 'The initial complaint filed'
            ]
        ];

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set documents
        $component->set('documents', $documents);
        
        // Verify session has the data
        $this->assertTrue(session()->has('wizard_documents'));
        $this->assertEquals($documents, session('wizard_documents'));
        
        // Create new component instance
        $newComponent = Livewire::test(CaseCreationWizard::class);
        
        // Should restore documents from session
        $newComponent->assertSet('documents', $documents);
    }

    /** @test */
    public function wizard_syncs_individual_properties_with_case_data()
    {
        $this->actingAs($this->proSeUser);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set basic info
        $basicInfo = [
            'name' => 'Sync Test Case',
            'case_number' => '',
            'type' => 'civil',
            'jurisdiction' => 'State',
            'venue' => 'District Court',
            'description' => 'Testing sync functionality'
        ];
        
        $component->set('basicInfo', $basicInfo);
        
        // Trigger an event that would cause syncing (like updateBasicInfo)
        $component->call('updateBasicInfo', $basicInfo);
        
        // Verify caseData is synced
        $component->assertSet('caseData.name', 'Sync Test Case');
        $component->assertSet('caseData.type', 'civil');
        $component->assertSet('caseData.jurisdiction', 'State');
    }

    /** @test */
    public function clear_wizard_session_removes_all_data()
    {
        $this->actingAs($this->legalProfessional);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set up some data
        $basicInfo = [
            'name' => 'Test Case',
            'case_number' => 'CV-2024-001',
            'type' => 'civil',
            'jurisdiction' => 'Federal',
            'venue' => 'District Court',
            'description' => 'Test description'
        ];
        
        $parties = [
            ['id' => 'party-1', 'name' => 'Test Plaintiff', 'type' => 'plaintiff'],
            ['id' => 'party-2', 'name' => 'Test Defendant', 'type' => 'defendant']
        ];
        
        $component->set('basicInfo', $basicInfo);
        $component->set('parties', $parties);
        $component->set('currentStep', 3);
        
        // Verify data is set
        $component->assertSet('currentStep', 3);
        $component->assertSet('basicInfo.name', 'Test Case');
        $component->assertCount('parties', 2);
        
        // Clear session
        $component->call('clearWizardSession');
        
        // Verify component properties are reset
        $component->assertSet('currentStep', 1);
        $component->assertSet('basicInfo.name', '');
        $component->assertSet('basicInfo.case_number', '');
        $component->assertSet('basicInfo.type', '');
        $component->assertCount('parties', 0);
        $component->assertCount('keyDates', 0);
        $component->assertCount('documents', 0);
        
        // Verify the wizard_last_activity session key is cleared
        $this->assertFalse(session()->has('wizard_last_activity'));
    }

    /** @test */
    public function wizard_tracks_last_activity_timestamp()
    {
        $this->actingAs($this->proSeUser);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set required basic information to pass validation
        $basicInfo = [
            'name' => 'Timestamp Test Case',
            'case_number' => '',
            'type' => 'civil',
            'jurisdiction' => 'State',
            'venue' => 'District Court',
            'description' => 'Testing timestamp functionality'
        ];
        
        $component->set('caseData', array_merge($component->get('caseData'), $basicInfo));
        
        // Move to next step (should trigger persistWizardState)
        $component->call('nextStep');
        
        // Should have last activity timestamp
        $this->assertTrue(session()->has('wizard_last_activity'));
        
        $lastActivity = session('wizard_last_activity');
        $this->assertNotNull($lastActivity);
    }

    /** @test */
    public function wizard_allows_navigation_to_previous_steps()
    {
        $this->actingAs($this->proSeUser);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set required basic information to pass validation
        $basicInfo = [
            'name' => 'Navigation Test Case',
            'case_number' => '',
            'type' => 'civil',
            'jurisdiction' => 'State',
            'venue' => 'District Court',
            'description' => 'Testing navigation functionality'
        ];
        
        $component->set('caseData', array_merge($component->get('caseData'), $basicInfo));
        
        // Move to step 2
        $component->call('nextStep');
        $component->assertSet('currentStep', 2);
        
        // Navigate back to step 1
        $component->call('goToStep', 1);
        $component->assertSet('currentStep', 1);
        
        // Should be able to go back to step 2 (since we've been there before)
        $component->call('goToStep', 2);
        $component->assertSet('currentStep', 2);
    }

    /** @test */
    public function wizard_prevents_invalid_step_navigation()
    {
        $this->actingAs($this->proSeUser);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Try to go to invalid steps
        $component->call('goToStep', 0);
        $component->assertSet('currentStep', 1); // Should stay at 1
        
        $component->call('goToStep', 10);
        $component->assertSet('currentStep', 1); // Should stay at 1
        
        $component->call('goToStep', -1);
        $component->assertSet('currentStep', 1); // Should stay at 1
    }

    /** @test */
    public function wizard_session_survives_page_refresh_scenario()
    {
        $this->actingAs($this->legalProfessional);

        // First session - fill out some data
        $component = Livewire::test(CaseCreationWizard::class);
        
        $basicInfo = [
            'name' => 'Page Refresh Test',
            'case_number' => 'CV-2024-003',
            'type' => 'civil',
            'jurisdiction' => 'Federal',
            'venue' => 'District Court',
            'description' => 'Testing page refresh persistence'
        ];
        
        $component->set('basicInfo', $basicInfo);
        $component->set('caseData', array_merge($component->get('caseData'), $basicInfo));
        $component->call('nextStep'); // Move to step 2
        $component->assertSet('currentStep', 2);
        
        // Simulate page refresh by creating new component instance
        $refreshedComponent = Livewire::test(CaseCreationWizard::class);
        
        // Should restore all data
        $refreshedComponent->assertSet('currentStep', 2);
        $refreshedComponent->assertSet('basicInfo.name', 'Page Refresh Test');
        $refreshedComponent->assertSet('basicInfo.case_number', 'CV-2024-003');
        
        // Should be able to continue normally (set data for next step validation)
        $refreshedComponent->set('caseData', array_merge($refreshedComponent->get('caseData'), [
            'parties' => [
                ['id' => 'party-1', 'name' => 'Test Plaintiff', 'type' => 'plaintiff'],
                ['id' => 'party-2', 'name' => 'Test Defendant', 'type' => 'defendant']
            ]
        ]));
        $refreshedComponent->call('nextStep');
        $refreshedComponent->assertSet('currentStep', 3);
    }

    /** @test */
    public function wizard_clears_session_after_successful_submission()
    {
        $this->actingAs($this->legalProfessional);

        $component = Livewire::test(CaseCreationWizard::class);
        
        // Set up complete valid data for submission
        $basicInfo = [
            'name' => 'Submission Test Case',
            'case_number' => 'CV-2024-003',
            'type' => 'civil',
            'jurisdiction' => 'Federal',
            'venue' => 'District Court',
            'description' => 'Testing submission functionality'
        ];
        
        $parties = [
            ['id' => 'party-1', 'name' => 'Test Plaintiff', 'type' => 'plaintiff'],
            ['id' => 'party-2', 'name' => 'Test Defendant', 'type' => 'defendant']
        ];
        
        $component->set('basicInfo', $basicInfo);
        $component->set('parties', $parties);
        $component->set('caseData', array_merge($component->get('caseData'), $basicInfo, ['parties' => $parties]));
        $component->set('currentStep', 5); // Review step
        
        // Submit the wizard
        $component->call('submitWizard');
        
        // Component properties should be reset after successful submission
        $component->assertSet('currentStep', 1);
        $component->assertSet('basicInfo.name', '');
        $component->assertSet('basicInfo.case_number', '');
        $component->assertCount('parties', 0);
        
        // Verify the wizard_last_activity session key is cleared
        $this->assertFalse(session()->has('wizard_last_activity'));
    }
}
