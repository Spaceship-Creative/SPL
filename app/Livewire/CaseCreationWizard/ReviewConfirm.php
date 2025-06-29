<?php

namespace App\Livewire\CaseCreationWizard;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReviewConfirm extends Component
{
    // Case data passed from parent wizard
    public $caseData = [];
    
    // User type information
    public $userType = 'pro_se';
    public $isLegalProfessional = false;

    public function mount($caseData = [], $userType = 'pro_se', $isLegalProfessional = false)
    {
        $this->caseData = $caseData;
        $this->userType = $userType;
        $this->isLegalProfessional = $isLegalProfessional;
    }

    /**
     * Get formatted case data for display
     */
    #[Computed]
    public function basicInformation()
    {
        return [
            'name' => $this->caseData['name'] ?? '',
            'case_number' => $this->caseData['case_number'] ?? 'Not specified',
            'type' => $this->caseData['type'] ?? '',
            'jurisdiction' => $this->caseData['jurisdiction'] ?? '',
            'venue' => $this->caseData['venue'] ?? '',
            'description' => $this->caseData['description'] ?? ''
        ];
    }

    /**
     * Get parties organized by type
     */
    #[Computed]
    public function organizedParties()
    {
        $parties = $this->caseData['parties'] ?? [];
        
        return [
            'plaintiffs' => collect($parties)->where('type', 'plaintiff')->values()->toArray(),
            'defendants' => collect($parties)->where('type', 'defendant')->values()->toArray(),
            'attorneys' => collect($parties)->where('type', 'attorney')->values()->toArray(),
            'judges' => collect($parties)->where('type', 'judge')->values()->toArray(),
            'witnesses' => collect($parties)->where('type', 'witness')->values()->toArray(),
            'others' => collect($parties)->whereNotIn('type', ['plaintiff', 'defendant', 'attorney', 'judge', 'witness'])->values()->toArray()
        ];
    }

    /**
     * Get key dates organized by priority
     */
    #[Computed]
    public function organizedKeyDates()
    {
        $keyDates = $this->caseData['key_dates'] ?? [];
        
        return [
            'high_priority' => collect($keyDates)->where('priority', 'high')->sortBy('date')->values()->toArray(),
            'medium_priority' => collect($keyDates)->where('priority', 'medium')->sortBy('date')->values()->toArray(),
            'low_priority' => collect($keyDates)->where('priority', 'low')->sortBy('date')->values()->toArray()
        ];
    }

    /**
     * Get documents organized by category
     */
    #[Computed]
    public function organizedDocuments()
    {
        $documents = $this->caseData['documents'] ?? [];
        
        return [
            'complaints' => collect($documents)->where('category', 'complaint')->values()->toArray(),
            'motions' => collect($documents)->where('category', 'motion')->values()->toArray(),
            'orders' => collect($documents)->where('category', 'order')->values()->toArray(),
            'evidence' => collect($documents)->where('category', 'evidence')->values()->toArray(),
            'correspondence' => collect($documents)->where('category', 'correspondence')->values()->toArray(),
            'other' => collect($documents)->whereNotIn('category', ['complaint', 'motion', 'order', 'evidence', 'correspondence'])->values()->toArray()
        ];
    }

    /**
     * Check if case data is complete and ready for submission
     */
    #[Computed]
    public function isDataComplete()
    {
        $basicInfo = $this->basicInformation;
        $parties = $this->organizedParties;
        
        // Check required basic information
        $hasRequiredBasics = !empty($basicInfo['name']) && 
                           !empty($basicInfo['type']) && 
                           !empty($basicInfo['jurisdiction']) && 
                           !empty($basicInfo['venue']) && 
                           !empty($basicInfo['description']);

        // For legal professionals, case number is required
        if ($this->isLegalProfessional) {
            $hasRequiredBasics = $hasRequiredBasics && !empty($basicInfo['case_number']) && $basicInfo['case_number'] !== 'Not specified';
        }

        // Check that we have at least one plaintiff and one defendant
        $hasRequiredParties = !empty($parties['plaintiffs']) && !empty($parties['defendants']);

        return $hasRequiredBasics && $hasRequiredParties;
    }

    /**
     * Get validation warnings for display
     */
    #[Computed]
    public function validationWarnings()
    {
        $warnings = [];
        $basicInfo = $this->basicInformation;
        $parties = $this->organizedParties;
        $keyDates = $this->organizedKeyDates;
        $documents = $this->organizedDocuments;

        // Check basic information
        if (empty($basicInfo['name'])) {
            $warnings[] = 'Case name is required';
        }
        if ($this->isLegalProfessional && (empty($basicInfo['case_number']) || $basicInfo['case_number'] === 'Not specified')) {
            $warnings[] = 'Case number is required for legal professionals';
        }
        if (empty($basicInfo['type'])) {
            $warnings[] = 'Case type is required';
        }
        if (empty($basicInfo['jurisdiction'])) {
            $warnings[] = 'Jurisdiction is required';
        }
        if (empty($basicInfo['venue'])) {
            $warnings[] = 'Venue is required';
        }
        if (empty($basicInfo['description'])) {
            $warnings[] = 'Case description is required';
        }

        // Check parties
        if (empty($parties['plaintiffs'])) {
            $warnings[] = 'At least one plaintiff is required';
        }
        if (empty($parties['defendants'])) {
            $warnings[] = 'At least one defendant is required';
        }

        // Optional warnings for better completeness
        if (empty($keyDates['high_priority']) && empty($keyDates['medium_priority']) && empty($keyDates['low_priority'])) {
            $warnings[] = 'Consider adding important dates for your case';
        }

        return $warnings;
    }

    /**
     * Navigate back to a specific step to make edits
     */
    public function editStep($step)
    {
        $this->dispatch('go-to-step', $step);
    }

    public function render()
    {
        return view('livewire.case-creation-wizard.review-confirm');
    }
}
