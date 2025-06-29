<?php

namespace App\Livewire;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CaseCreationWizard extends Component
{
    public $currentStep = 1;
    public $totalSteps = 5;
    
    // Case data properties
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
    
    // Form data properties for each step
    public $basicInfo = [
        'name' => '',
        'case_number' => '',
        'type' => '',
        'jurisdiction' => '',
        'venue' => '',
        'description' => ''
    ];
    
    public $parties = [];
    public $keyDates = [];
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
        // Initialize wizard state
        $this->currentStep = 1;
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
     * Get user type display name for UI
     */
    #[Computed]
    public function userTypeDisplay()
    {
        return Auth::user()?->user_type_display ?? 'Pro-Se Litigant';
    }

    public function nextStep()
    {
        // For step 5 (review), go directly to submit
        if ($this->currentStep === 5) {
            $this->submitWizard();
            return;
        }

        // Validate current step before proceeding
        $isValid = match($this->currentStep) {
            1 => $this->validateBasicInformation(),
            2 => $this->validatePartyManagement(), 
            3 => $this->validateKeyDates(),
            4 => $this->validateDocumentUpload(),
            default => false
        };

        if (!$isValid) {
            return;
        }

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->dispatch('step-changed', $this->currentStep);
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->dispatch('step-changed', $this->currentStep);
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->dispatch('step-changed', $this->currentStep);
        }
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

            session()->flash('success', 'Case created successfully!');
            $this->redirect(route('cases.show', $legalCase), navigate: true);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create case. Please try again.');
        }
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

        // Ensure we have at least one plaintiff and one defendant
        $hasPlaintiff = collect($this->caseData['parties'])->contains('type', 'plaintiff');
        $hasDefendant = collect($this->caseData['parties'])->contains('type', 'defendant');

        if (!$hasPlaintiff) {
            session()->flash('error', 'Please add at least one plaintiff to the case.');
            return false;
        }

        if (!$hasDefendant) {
            session()->flash('error', 'Please add at least one defendant to the case.');
            return false;
        }

        return true;
    }

    private function validateKeyDates()
    {
        // For legal professionals, dates are optional (they may set them later)
        if ($this->isLegalProfessional) {
            return true;
        }

        // For pro-se users, encourage at least one key date
        if (empty($this->caseData['key_dates'])) {
            session()->flash('warning', 'Consider adding at least one important date for your case.');
        }

        return true;
    }

    private function validateDocumentUpload()
    {
        // Document upload validation is optional for both user types
        // Future implementation will handle actual file uploads
        return true;
    }

    private function validateAllSteps()
    {
        return $this->validateBasicInformation() && 
               $this->validatePartyManagement() && 
               $this->validateKeyDates() && 
               $this->validateDocumentUpload();
    }

    // Event handlers for child component updates
    public function updateBasicInfo($basicInfo)
    {
        $this->basicInfo = $basicInfo;
        
        // Sync with caseData for validation
        $this->caseData = array_merge($this->caseData, $basicInfo);
    }

    public function updateParties($parties)
    {
        $this->parties = $parties;
        $this->caseData['parties'] = $parties;
    }

    public function updateKeyDates($keyDates)
    {
        $this->keyDates = $keyDates;
        $this->caseData['key_dates'] = $keyDates;
    }

    public function updateDocuments($documents)
    {
        $this->documents = $documents;
        $this->caseData['documents'] = $documents;
    }

    public function render()
    {
        return view('livewire.case-creation-wizard');
    }
}
