<?php

namespace App\Livewire\CaseCreationWizard;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Validate;

class KeyDates extends Component
{
    // User type properties
    public $userType = 'pro_se';
    public $isLegalProfessional = false;

    // Key dates array
    public $keyDates = [];
    
    // New date form properties
    #[Validate('required|string|min:3|max:255', message: 'Please enter a title for this date.')]
    public $title = '';
    
    #[Validate('required|date|after_or_equal:today', message: 'Please enter a valid future date.')]
    public $date = '';
    
    #[Validate('nullable|string|regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', message: 'Please enter time in HH:MM format.')]
    public $time = '';
    
    #[Validate('required|string', message: 'Please select a date type.')]
    public $type = '';
    
    #[Validate('required|string', message: 'Please select a priority level.')]
    public $priority = 'medium';
    
    #[Validate('nullable|string|max:500')]
    public $description = '';

    public function getDateTypesProperty()
    {
        if ($this->isLegalProfessional) {
            // Comprehensive list for legal professionals
            return [
                'filing_deadline' => 'Filing Deadline',
                'discovery_deadline' => 'Discovery Deadline',
                'motion_deadline' => 'Motion Deadline',
                'response_due' => 'Response Due',
                'hearing_date' => 'Hearing Date',
                'trial_date' => 'Trial Date',
                'deposition_date' => 'Deposition Date',
                'mediation_date' => 'Mediation Date',
                'arbitration_date' => 'Arbitration Date',
                'settlement_conference' => 'Settlement Conference',
                'case_management_conference' => 'Case Management Conference',
                'status_conference' => 'Status Conference',
                'appeal_deadline' => 'Appeal Deadline',
                'expert_disclosure' => 'Expert Disclosure Deadline',
                'witness_list_due' => 'Witness List Due',
                'other' => 'Other'
            ];
        } else {
            // Simplified list for pro-se users with explanations
            return [
                'court_date' => 'Court Date (When you need to appear)',
                'filing_deadline' => 'Filing Deadline (When papers are due)',
                'response_due' => 'Response Due (When the other party must respond)',
                'hearing_date' => 'Hearing (When the judge will listen to arguments)',
                'trial_date' => 'Trial Date (When your case will be decided)',
                'mediation_date' => 'Mediation (Meeting to try to settle)',
                'important_deadline' => 'Important Deadline',
                'other' => 'Other Important Date'
            ];
        }
    }

    public function getPriorityOptionsProperty()
    {
        if ($this->isLegalProfessional) {
            return [
                'critical' => 'Critical',
                'high' => 'High',
                'medium' => 'Medium',
                'low' => 'Low'
            ];
        } else {
            return [
                'critical' => 'Must Not Miss (Critical)',
                'high' => 'Very Important',
                'medium' => 'Important',
                'low' => 'Nice to Remember'
            ];
        }
    }

    public function mount($userType = 'pro_se', $isLegalProfessional = false)
    {
        $this->userType = $userType;
        $this->isLegalProfessional = $isLegalProfessional;
    }

    public function addKeyDate()
    {
        // Validate using property attributes
        $this->validate();

        // Add to key dates array
        $newDate = [
            'id' => uniqid(),
            'title' => $this->title,
            'date' => $this->date,
            'time' => $this->time,
            'type' => $this->type,
            'priority' => $this->priority,
            'description' => $this->description,
            'created_at' => now()->toDateTimeString()
        ];

        $this->keyDates[] = $newDate;

        // Sort dates chronologically
        $this->sortKeyDates();

        // Reset form
        $this->reset(['title', 'date', 'time', 'type', 'priority', 'description']);

        // Show success message
        session()->flash('date-success', 'Date added successfully!');

        // Emit to parent
        $this->dispatch('updateKeyDates', $this->keyDates);
    }

    public function removeKeyDate($dateId)
    {
        $this->keyDates = collect($this->keyDates)->reject(function ($date) use ($dateId) {
            return $date['id'] === $dateId;
        })->values()->toArray();

        session()->flash('date-success', 'Date removed successfully.');
        $this->dispatch('key-dates-updated', $this->keyDates);
    }

    private function sortKeyDates()
    {
        // Sort by priority (critical first) then by date
        $priorityOrder = ['critical' => 1, 'high' => 2, 'medium' => 3, 'low' => 4];

        usort($this->keyDates, function ($a, $b) use ($priorityOrder) {
            $priorityA = $priorityOrder[$a['priority']] ?? 5;
            $priorityB = $priorityOrder[$b['priority']] ?? 5;

            if ($priorityA !== $priorityB) {
                return $priorityA - $priorityB;
            }

            return strcmp($a['date'], $b['date']);
        });
    }

    public function validateKeyDates()
    {
        // For legal professionals, dates are always optional
        if ($this->isLegalProfessional) {
            return true;
        }

        // For pro-se users, encourage at least one key date
        if (empty($this->keyDates)) {
            session()->flash('date-warning', 'Consider adding at least one important date to help you stay organized.');
        }

        return true;
    }

    public function getDateDisplayType($type)
    {
        $types = $this->dateTypes;
        return $types[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public function getPriorityDisplayName($priority)
    {
        $priorities = $this->priorityOptions;
        return $priorities[$priority] ?? ucfirst($priority);
    }

    public function formatDate($date, $time = null)
    {
        try {
            $carbon = Carbon::parse($date);
            $formatted = $carbon->format('M j, Y');
            
            if ($time) {
                $formatted .= ' at ' . Carbon::parse($time)->format('g:i A');
            }
            
            return $formatted;
        } catch (\Exception $e) {
            return $date . ($time ? ' at ' . $time : '');
        }
    }

    public function getDaysUntil($date)
    {
        try {
            $targetDate = Carbon::parse($date);
            $today = Carbon::today();
            $days = $today->diffInDays($targetDate, false);
            
            if ($days < 0) {
                return 'Past due';
            } elseif ($days === 0) {
                return 'Today';
            } elseif ($days === 1) {
                return 'Tomorrow';
            } else {
                return "In {$days} days";
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    public function render()
    {
        return view('livewire.case-creation-wizard.key-dates', [
            'dateTypes' => $this->dateTypes,
            'priorityOptions' => $this->priorityOptions
        ]);
    }
}
