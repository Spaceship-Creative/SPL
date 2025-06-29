<?php

namespace App\Livewire\CaseCreationWizard;

use Livewire\Component;
use Livewire\Attributes\Validate;

class BasicInformation extends Component
{
    // User type properties
    public $userType = 'pro_se';
    public $isLegalProfessional = false;

    // Form properties with validation attributes
    #[Validate('required|string|min:3|max:255', message: 'Please enter a case name between 3 and 255 characters.')]
    public $name = '';

    // Case number validation handled dynamically in rules() method
    public $caseNumber = '';

    #[Validate('required|string', message: 'Please select a case type.')]
    public $type = '';

    #[Validate('required|string', message: 'Please select a jurisdiction.')]
    public $jurisdiction = '';

    #[Validate('required|string', message: 'Please enter the venue/court.')]
    public $venue = '';

    #[Validate('required|string|min:10|max:1000', message: 'Please provide a case description between 10 and 1000 characters.')]
    public $description = '';

    // Case type options - conditional based on user type
    public function getCaseTypesProperty()
    {
        if ($this->isLegalProfessional) {
            // More comprehensive list for legal professionals
            return [
                'civil_litigation' => 'Civil Litigation',
                'contract_dispute' => 'Contract Dispute',
                'personal_injury' => 'Personal Injury',
                'employment' => 'Employment Law',
                'business_dispute' => 'Business Dispute',
                'real_estate' => 'Real Estate',
                'family_law' => 'Family Law',
                'probate' => 'Probate & Estate',
                'intellectual_property' => 'Intellectual Property',
                'bankruptcy' => 'Bankruptcy',
                'criminal_defense' => 'Criminal Defense',
                'other' => 'Other'
            ];
        } else {
            // Simplified list for pro-se users with common self-representation cases
            return [
                'landlord_tenant' => 'Landlord/Tenant Dispute',
                'small_claims' => 'Small Claims',
                'family_law' => 'Family Law (Divorce, Custody)',
                'employment' => 'Employment Issue',
                'consumer_dispute' => 'Consumer Complaint',
                'personal_injury' => 'Personal Injury',
                'contract_dispute' => 'Contract Issue',
                'debt_collection' => 'Debt Collection',
                'other' => 'Other'
            ];
        }
    }

    // Jurisdiction options
    public function getJurisdictionsProperty()
    {
        return [
            'federal' => 'Federal Court',
            'state' => 'State Court',
            'local' => 'Local/Municipal Court',
            'administrative' => 'Administrative Body'
        ];
    }

    public function mount($userType = 'pro_se', $isLegalProfessional = false)
    {
        $this->userType = $userType;
        $this->isLegalProfessional = $isLegalProfessional;
    }

    public function updated($field)
    {
        // Real-time validation for specific fields
        if (in_array($field, ['name', 'type', 'jurisdiction', 'venue', 'description'])) {
            $this->validateOnly($field);
        }
        
        // Also validate case number if it's a legal professional
        if ($field === 'caseNumber' && $this->isLegalProfessional) {
            $this->validateOnly($field);
        }

        // Emit to parent component
        $this->dispatch('basic-info-updated', [
            'name' => $this->name,
            'case_number' => $this->caseNumber,
            'type' => $this->type,
            'jurisdiction' => $this->jurisdiction,
            'venue' => $this->venue,
            'description' => $this->description
        ]);
    }

    public function validateAll()
    {
        try {
            $rules = [
                'name' => 'required|string|min:3|max:255',
                'type' => 'required|string',
                'jurisdiction' => 'required|string',
                'venue' => 'required|string',
                'description' => 'required|string|min:10|max:1000'
            ];

            // Add case number validation based on user type
            if ($this->isLegalProfessional) {
                $rules['caseNumber'] = 'required|string|max:100';
            } else {
                $rules['caseNumber'] = 'nullable|string|max:100';
            }

            $this->validate($rules, [], [
                'name' => 'case name',
                'caseNumber' => 'case number',
                'type' => 'case type',
                'jurisdiction' => 'jurisdiction',
                'venue' => 'venue',
                'description' => 'case description'
            ]);

            $this->dispatch('validation-passed', 'basic-information');
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-failed', 'basic-information');
            return false;
        }
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'type' => 'required|string',
            'jurisdiction' => 'required|string', 
            'venue' => 'required|string',
            'description' => 'required|string|min:10|max:1000'
        ];

        // Conditional case number validation
        if ($this->isLegalProfessional) {
            $rules['caseNumber'] = 'required|string|max:100';
        } else {
            $rules['caseNumber'] = 'nullable|string|max:100';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Please enter a case name.',
            'name.min' => 'Case name must be at least 3 characters long.',
            'name.max' => 'Case name cannot exceed 255 characters.',
            'caseNumber.required' => 'Case number is required for legal professionals.',
            'caseNumber.max' => 'Case number cannot exceed 100 characters.',
            'type.required' => 'Please select a case type.',
            'jurisdiction.required' => 'Please select a jurisdiction.',
            'venue.required' => 'Please enter the venue or court name.',
            'description.required' => 'Please provide a case description.',
            'description.min' => 'Case description must be at least 10 characters long.',
            'description.max' => 'Case description cannot exceed 1000 characters.'
        ];
    }

    public function render()
    {
        return view('livewire.case-creation-wizard.basic-information', [
            'caseTypes' => $this->caseTypes,
            'jurisdictions' => $this->jurisdictions
        ]);
    }
}
