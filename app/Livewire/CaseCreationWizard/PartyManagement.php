<?php

namespace App\Livewire\CaseCreationWizard;

use Livewire\Component;
use Livewire\Attributes\Validate;

class PartyManagement extends Component
{
    // User type properties
    public $userType = 'pro_se';
    public $isLegalProfessional = false;

    // Party list
    public $parties = [];
    
    // New party form properties
    #[Validate('required|string', message: 'Please select a party type.')]
    public $newPartyType = '';
    
    #[Validate('required|string|max:255', message: 'Party name is required and must be less than 255 characters.')]
    public $newPartyName = '';
    
    #[Validate('required|string', message: 'Please select a party category.')]
    public $newPartyCategory = 'individual';
    
    #[Validate('nullable|email|max:255', message: 'Please enter a valid email address.')]
    public $newPartyEmail = '';
    
    #[Validate('nullable|string|max:20', message: 'Phone number must be less than 20 characters.')]
    public $newPartyPhone = '';
    
    #[Validate('nullable|string|max:500', message: 'Address must be less than 500 characters.')]
    public $newPartyAddress = '';

    // Party type options - conditional based on user type
    public function getPartyTypesProperty()
    {
        if ($this->isLegalProfessional) {
            // Comprehensive list for legal professionals
            return [
                'plaintiff' => 'Plaintiff',
                'defendant' => 'Defendant',
                'petitioner' => 'Petitioner',
                'respondent' => 'Respondent',
                'appellant' => 'Appellant',
                'appellee' => 'Appellee',
                'third_party_defendant' => 'Third Party Defendant',
                'intervenor' => 'Intervenor',
                'witness' => 'Witness',
                'expert_witness' => 'Expert Witness',
                'other' => 'Other'
            ];
        } else {
            // Simplified list for pro-se users
            return [
                'plaintiff' => 'Plaintiff (The person bringing the case)',
                'defendant' => 'Defendant (The person being sued)',
                'petitioner' => 'Petitioner (Person requesting court action)',
                'respondent' => 'Respondent (Person responding to petition)',
                'witness' => 'Witness',
                'other' => 'Other'
            ];
        }
    }

    // Category options based on user type
    public function getCategoriesProperty()
    {
        if ($this->isLegalProfessional) {
            return [
                'individual' => 'Individual',
                'corporation' => 'Corporation',
                'llc' => 'LLC',
                'partnership' => 'Partnership',
                'government_entity' => 'Government Entity',
                'non_profit' => 'Non-Profit Organization',
                'trust' => 'Trust',
                'estate' => 'Estate',
                'other_entity' => 'Other Entity'
            ];
        } else {
            return [
                'person' => 'Person/Individual',
                'business' => 'Business/Company',
                'government' => 'Government Agency',
                'other' => 'Other'
            ];
        }
    }

    protected function messages()
    {
        return [
            'newPartyType.required' => 'Please select what type of party this is.',
            'newPartyName.required' => 'Please provide the party\'s full name.',
            'newPartyName.max' => 'The party name cannot exceed 255 characters.',
            'newPartyCategory.required' => 'Please select whether this is an individual, organization, or government entity.',
            'newPartyEmail.email' => 'Please enter a valid email address.',
            'newPartyPhone.max' => 'Phone number cannot exceed 20 characters.',
            'newPartyAddress.max' => 'Address cannot exceed 500 characters.',
        ];
    }

    public function mount($userType = 'pro_se', $isLegalProfessional = false)
    {
        $this->userType = $userType;
        $this->isLegalProfessional = $isLegalProfessional;

        // Initialize with parent wizard data if available
        $parent = $this->getParentWizard();
        if ($parent && isset($parent->parties)) {
            $this->parties = $parent->parties;
        }
    }

    public function updated($propertyName)
    {
        // Validate only the updated property if it's a new party field
        if (str_starts_with($propertyName, 'newParty')) {
            $this->validateOnly($propertyName);
        }
        
        // Update parent component when parties array changes
        if ($propertyName === 'parties') {
            $this->updateParent();
        }
    }

    public function addParty()
    {
        // Validate the form
        $this->validate([
            'newPartyName' => 'required|string|min:2|max:255',
            'newPartyType' => 'required|string',
            'newPartyCategory' => 'nullable|string|max:255',
            'newPartyEmail' => 'nullable|email|max:255',
            'newPartyPhone' => 'nullable|string|max:20',
            'newPartyAddress' => 'nullable|string|max:500'
        ], [], [
            'newPartyName' => 'party name',
            'newPartyType' => 'party type',
            'newPartyCategory' => 'party category',
            'newPartyEmail' => 'email address',
            'newPartyPhone' => 'phone number',
            'newPartyAddress' => 'address'
        ]);

        // Add to parties array
        $this->parties[] = [
            'id' => uniqid(),
            'name' => $this->newPartyName,
            'type' => $this->newPartyType,
            'category' => $this->newPartyCategory ?: 'individual',
            'email' => $this->newPartyEmail,
            'phone' => $this->newPartyPhone,
            'address' => $this->newPartyAddress,
        ];

        // Reset form
        $this->reset(['newPartyName', 'newPartyType', 'newPartyCategory', 'newPartyEmail', 'newPartyPhone', 'newPartyAddress']);

        // Show success message
        session()->flash('party-success', 'Party added successfully!');

        // Emit to parent
        $this->updateParent();
    }

    public function removeParty($partyId)
    {
        $this->parties = collect($this->parties)->reject(function ($party) use ($partyId) {
            return $party['id'] === $partyId;
        })->values()->toArray();

        session()->flash('party-success', 'Party removed successfully!');
        $this->updateParent();
    }

    public function validateParties()
    {
        if (empty($this->parties)) {
            session()->flash('party-error', 'Please add at least one party to the case.');
            return false;
        }

        // Business logic validation: require at least one plaintiff and one defendant
        $hasPlaintiff = collect($this->parties)->contains('type', 'plaintiff');
        $hasDefendant = collect($this->parties)->contains('type', 'defendant');

        // For cases that use petitioner/respondent instead
        $hasPetitioner = collect($this->parties)->contains('type', 'petitioner');
        $hasRespondent = collect($this->parties)->contains('type', 'respondent');

        if (!$hasPlaintiff && !$hasPetitioner) {
            $message = $this->isLegalProfessional 
                ? 'Please add at least one plaintiff or petitioner.'
                : 'Please add yourself or the person bringing this case (plaintiff/petitioner).';
            session()->flash('party-error', $message);
            return false;
        }

        if (!$hasDefendant && !$hasRespondent) {
            $message = $this->isLegalProfessional
                ? 'Please add at least one defendant or respondent.'
                : 'Please add the person or business you are taking action against (defendant/respondent).';
            session()->flash('party-error', $message);
            return false;
        }

        session()->forget('party-error');
        return true;
    }

    public function getPartyDisplayType($type)
    {
        $types = $this->partyTypes;
        return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public function getPartyDisplayCategory($category)
    {
        $categories = $this->categories;
        return $categories[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }

    protected function updateParent()
    {
        $this->dispatch('updateParties', $this->parties);
    }

    protected function getParentWizard()
    {
        return $this->parent ?? null;
    }

    public function render()
    {
        return view('livewire.case-creation-wizard.party-management', [
            'partyTypes' => $this->partyTypes,
            'categories' => $this->categories
        ]);
    }
}
