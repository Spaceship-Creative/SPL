<?php

namespace App\Livewire;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;

class CaseCreationWizard extends Component
{
    #[Session(key: 'wizard_current_step')]
    public $currentStep = 1;
    
    public $totalSteps = 5;
    
    // Case data properties with session persistence
    #[Session(key: 'wizard_case_data')]
    public $caseData = [
        'name' => '',
        'case_number' => '',
        'type' => '',
        'jurisdiction' => '',
        'venue' => '',
        'description' => '',
        'parties' => [],
        'key_dates' => [],
        'documents' => []
    ];
    
    // Wizard steps
    public $stepNames = [
        1 => 'Basic Information',
        2 => 'Party Management', 
        3 => 'Key Dates',
        4 => 'Document Upload',
        5 => 'Review & Confirm'
    ];
    
    // Form data properties for each step with session persistence
    #[Session(key: 'wizard_basic_info')]
    public $basicInfo = [
        'name' => '',
        'case_number' => '',
        'type' => '',
        'jurisdiction' => '',
        'venue' => '',
        'description' => ''
    ];
    
    #[Session(key: 'wizard_parties')]
    public $parties = [];
    
    #[Session(key: 'wizard_key_dates')]
    public $keyDates = [];
    
    #[Session(key: 'wizard_documents')]
    public $documents = [];

    protected $listeners = [
        'basic-info-updated' => 'updateBasicInfo',
        'updateParties',
        'key-dates-updated' => 'updateKeyDates',
        'documents-updated' => 'updateDocuments',
        'go-to-step' => 'goToStep'
    ];

    public function mount()
    {
        // Initialize wizard state - session data will be automatically loaded
        // If this is a fresh start, ensure we're on step 1
        if ($this->currentStep < 1 || $this->currentStep > $this->totalSteps) {
            $this->currentStep = 1;
        }
        
        // Sync individual properties with caseData array for consistency
        $this->syncDataFromSession();
    }

    /**
     * Sync individual form properties with the main caseData array
     */
    private function syncDataFromSession()
    {
        // Sync basic info
        if (!empty($this->basicInfo)) {
            foreach ($this->basicInfo as $key => $value) {
                if (!empty($value)) {
                    $this->caseData[$key] = $value;
                }
            }
        }
        
        // Sync other data
        if (!empty($this->parties)) {
            $this->caseData['parties'] = $this->parties;
        }
        
        if (!empty($this->keyDates)) {
            $this->caseData['key_dates'] = $this->keyDates;
        }
        
        if (!empty($this->documents)) {
            $this->caseData['documents'] = $this->documents;
        }
    }

    /**
     * Get the authenticated user's type for conditional rendering
     */
    #[Computed]
    public function userType()
    {
        return Auth::user()?->user_type ?? 'pro_se';
    }

    /**
     * Check if the current user is a legal professional
     */
    #[Computed] 
    public function isLegalProfessional()
    {
        return Auth::user()?->isLegalProfessional() ?? false;
    }

    /**
     * Check if the current user is a pro-se litigant
     */
    #[Computed]
    public function isProSe()
    {
        return Auth::user()?->isProSe() ?? true;
    }

    /**
     * Get display name for user type
     */
    #[Computed]
    public function userTypeDisplay()
    {
        return Auth::user()?->getUserTypeDisplayAttribute() ?? 'Pro-Se Litigant';
    }

    public function nextStep()
    {
        if ($this->validateCurrentStep()) {
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
                $this->persistWizardState();
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->persistWizardState();
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            // Allow going back to any previous step or next step if current is valid
            if ($step <= $this->currentStep || $this->validateCurrentStep()) {
                $this->currentStep = $step;
                $this->persistWizardState();
            }
        }
    }

    /**
     * Persist wizard state to session manually (in addition to automatic session attributes)
     */
    private function persistWizardState()
    {
        // The #[Session] attributes handle most persistence automatically,
        // but we can add additional state management here if needed
        session()->put('wizard_last_activity', now());
    }

    /**
     * Clear wizard session data
     */
    public function clearWizardSession()
    {
        // Reset component properties first (this will also clear session via #[Session] attributes)
        $this->currentStep = 1;
        $this->caseData = [
            'name' => '',
            'case_number' => '',
            'type' => '',
            'jurisdiction' => '',
            'venue' => '',
            'description' => '',
            'parties' => [],
            'key_dates' => [],
            'documents' => []
        ];
        $this->basicInfo = [
            'name' => '',
            'case_number' => '',
            'type' => '',
            'jurisdiction' => '',
            'venue' => '',
            'description' => ''
        ];
        $this->parties = [];
        $this->keyDates = [];
        $this->documents = [];
        
        // Clear additional session data that doesn't have #[Session] attributes
        session()->forget([
            'wizard_last_activity'
        ]);
    }

    public function submitWizard()
    {
        // Final validation of all steps
        if (!$this->validateAllSteps()) {
            session()->flash('error', 'Please complete all required fields before submitting.');
            return;
        }

        try {
            // Create the legal case
            $legalCase = LegalCase::create([
                'user_id' => Auth::id(),
                'name' => $this->caseData['name'],
                'case_number' => $this->caseData['case_number'],
                'type' => $this->caseData['type'],
                'jurisdiction' => $this->caseData['jurisdiction'],
                'venue' => $this->caseData['venue'],
                'description' => $this->caseData['description'],
                'status' => 'pending',
                'metadata' => [
                    'parties' => $this->caseData['parties'],
                    'key_dates' => $this->caseData['key_dates'],
                    'documents' => $this->caseData['documents'],
                    'created_by_user_type' => $this->userType
                ]
            ]);

            // Clear wizard session data after successful submission
            $this->clearWizardSession();

            session()->flash('success', 'Case created successfully!');
            $this->redirect(route('cases.show', $legalCase), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create case. Please try again.');
        }
    }

    /**
     * Get validation warnings for incomplete data
     */
    public function getValidationWarningsProperty()
    {
        $warnings = [];
        
        // Basic information warnings
        if (empty($this->caseData['name'])) {
            $warnings[] = 'Case name is required';
        }
        
        if ($this->isLegalProfessional && empty($this->caseData['case_number'])) {
            $warnings[] = 'Case number is required for legal professionals';
        }
        
        if (empty($this->caseData['type'])) {
            $warnings[] = 'Case type must be selected';
        }
        
        if (empty($this->caseData['jurisdiction'])) {
            $warnings[] = 'Jurisdiction is required';
        }
        
        // Party warnings
        $plaintiffs = collect($this->caseData['parties'])->where('type', 'plaintiff')->count();
        $defendants = collect($this->caseData['parties'])->where('type', 'defendant')->count();
        
        if ($plaintiffs === 0) {
            $warnings[] = 'At least one plaintiff is required';
        }
        
        if ($defendants === 0) {
            $warnings[] = 'At least one defendant is required';
        }
        
        return $warnings;
    }

    /**
     * Check if all required data is complete
     */
    public function getIsDataCompleteProperty()
    {
        return empty($this->validationWarnings);
    }

    // Event handlers for child component updates
    #[On('basic-info-updated')]
    public function updateBasicInfo($data)
    {
        $this->basicInfo = $data;
        $this->caseData = array_merge($this->caseData, $data);
        $this->persistWizardState();
    }

    #[On('updateParties')]
    public function updateParties($parties)
    {
        $this->parties = $parties;
        $this->caseData['parties'] = $parties;
        $this->persistWizardState();
    }

    #[On('key-dates-updated')]
    public function updateKeyDates($dates)
    {
        $this->keyDates = $dates;
        $this->caseData['key_dates'] = $dates;
        $this->persistWizardState();
    }

    #[On('documents-updated')]
    public function updateDocuments($documents)
    {
        $this->documents = $documents;
        $this->caseData['documents'] = $documents;
        $this->persistWizardState();
    }

    private function validateBasicInformation()
    {
        $rules = [
            'caseData.name' => 'required|string|min:3|max:255',
            'caseData.type' => 'required|string',
            'caseData.jurisdiction' => 'required|string',
            'caseData.venue' => 'required|string',
            'caseData.description' => 'required|string|min:10|max:1000'
        ];

        // Case number is optional for pro-se users, required for legal professionals
        if ($this->isLegalProfessional) {
            $rules['caseData.case_number'] = 'required|string|max:100';
        } else {
            $rules['caseData.case_number'] = 'nullable|string|max:100';
        }

        try {
            $this->validate($rules, [], [
                'caseData.name' => 'case name',
                'caseData.case_number' => 'case number',
                'caseData.type' => 'case type',
                'caseData.jurisdiction' => 'jurisdiction',
                'caseData.venue' => 'venue',
                'caseData.description' => 'case description'
            ]);
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Please fill in all required basic information fields.');
            return false;
        }
    }

    private function validatePartyManagement()
    {
        if (empty($this->caseData['parties'])) {
            session()->flash('error', 'Please add at least one party to the case.');
            return false;
        }

        // Check for required parties
        $plaintiffs = collect($this->caseData['parties'])->where('type', 'plaintiff');
        if ($plaintiffs->isEmpty()) {
            session()->flash('error', 'Please add at least one plaintiff to the case.');
            return false;
        }

        $defendants = collect($this->caseData['parties'])->where('type', 'defendant');
        if ($defendants->isEmpty()) {
            session()->flash('error', 'Please add at least one defendant to the case.');
            return false;
        }

        return true;
    }

    private function validateKeyDates()
    {
        // Key dates are optional but we can warn if none are provided
        if (empty($this->caseData['key_dates']) && !$this->isLegalProfessional) {
            session()->flash('warning', 'Consider adding at least one important date for your case.');
        }
        
        return true;
    }

    private function validateDocuments()
    {
        // Documents are optional for now
        return true;
    }

    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                return $this->validateBasicInformation();
            case 2:
                return $this->validatePartyManagement();
            case 3:
                return $this->validateKeyDates();
            case 4:
                return $this->validateDocuments();
            case 5:
                return true; // Review step
            default:
                return false;
        }
    }

    private function validateAllSteps()
    {
        return $this->validateBasicInformation() && 
               $this->validatePartyManagement() && 
               $this->validateKeyDates() && 
               $this->validateDocuments();
    }

    public function render()
    {
        return view('livewire.case-creation-wizard');
    }
}
