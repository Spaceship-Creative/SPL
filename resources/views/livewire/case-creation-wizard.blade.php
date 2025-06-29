<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
    {{-- User Type Badge --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                {{ $this->isLegalProfessional ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                {{ $this->userTypeDisplay }}
            </span>
            @if(session()->has('wizard_last_activity'))
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L10 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    Progress Saved
                </span>
            @endif
        </div>
        
        {{-- Clear Progress Button --}}
        @if($currentStep > 1 || !empty($this->caseData['name']) || !empty($this->parties) || !empty($this->keyDates))
            <button wire:click="clearWizardSession" 
                    wire:confirm="Are you sure you want to clear all progress? This cannot be undone."
                    class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Clear Progress
            </button>
        @endif
    </div>

    {{-- Welcome Message --}}
    <div class="text-center mb-8">
        @if($this->isLegalProfessional)
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Case</h1>
            <p class="text-gray-600">Efficiently add a new case to your management system.</p>
        @else
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Your Case</h1>
            <p class="text-gray-600">We'll guide you through setting up your legal case step by step. Your progress is automatically saved.</p>
        @endif
    </div>

    {{-- Session State Notification --}}
    @if($currentStep > 1 && session()->has('wizard_last_activity'))
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <h4 class="text-blue-800 font-medium mb-1">Progress Restored</h4>
                    <p class="text-blue-700 text-sm">
                        Your previous work has been restored. You can continue from where you left off or navigate to any previous step to make changes.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Progress Indicator --}}
    @if($this->isLegalProfessional)
        {{-- Horizontal compact navigation for legal professionals --}}
        <div class="mb-8">
            <nav aria-label="Progress">
                <ol class="flex items-center justify-between">
                    @foreach($stepNames as $stepNumber => $stepName)
                        <li class="relative flex-1 {{ $stepNumber < count($stepNames) ? 'pr-8 sm:pr-20' : '' }}">
                            @if($stepNumber < $currentStep)
                                {{-- Completed step --}}
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="h-0.5 w-full bg-indigo-600"></div>
                                </div>
                                <a wire:click="goToStep({{ $stepNumber }})" 
                                   class="relative w-8 h-8 flex items-center justify-center bg-indigo-600 rounded-full hover:bg-indigo-900 cursor-pointer">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">{{ $stepName }}</span>
                                </a>
                            @elseif($stepNumber == $currentStep)
                                {{-- Current step --}}
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="h-0.5 w-full bg-gray-200"></div>
                                </div>
                                <a class="relative w-8 h-8 flex items-center justify-center bg-white border-2 border-indigo-600 rounded-full" aria-current="step">
                                    <span class="h-2.5 w-2.5 bg-indigo-600 rounded-full" aria-hidden="true"></span>
                                    <span class="sr-only">{{ $stepName }}</span>
                                </a>
                            @else
                                {{-- Upcoming step --}}
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="h-0.5 w-full bg-gray-200"></div>
                                </div>
                                <a class="group relative w-8 h-8 flex items-center justify-center bg-white border-2 border-gray-300 rounded-full hover:border-gray-400">
                                    <span class="h-2.5 w-2.5 bg-transparent rounded-full group-hover:bg-gray-300" aria-hidden="true"></span>
                                    <span class="sr-only">{{ $stepName }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    @else
        {{-- Detailed vertical navigation for pro-se users --}}
        <div class="mb-8">
            <nav aria-label="Progress" class="mb-8">
                <ol class="space-y-4 md:flex md:space-y-0 md:space-x-8">
                    @foreach($stepNames as $stepNumber => $stepName)
                        <li class="md:flex-1">
                            @if($stepNumber < $currentStep)
                                {{-- Completed step --}}
                                <a wire:click="goToStep({{ $stepNumber }})" 
                                   class="group pl-4 py-2 flex flex-col border-l-4 border-indigo-600 hover:border-indigo-800 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4 cursor-pointer">
                                    <span class="text-xs text-indigo-600 font-semibold tracking-wide uppercase group-hover:text-indigo-800">Step {{ $stepNumber }}</span>
                                    <span class="text-sm font-medium">{{ $stepName }}</span>
                                </a>
                            @elseif($stepNumber == $currentStep)
                                {{-- Current step --}}
                                <a class="pl-4 py-2 flex flex-col border-l-4 border-indigo-600 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4" aria-current="step">
                                    <span class="text-xs text-indigo-600 font-semibold tracking-wide uppercase">Step {{ $stepNumber }}</span>
                                    <span class="text-sm font-medium">{{ $stepName }}</span>
                                </a>
                            @else
                                {{-- Upcoming step --}}
                                <a class="group pl-4 py-2 flex flex-col border-l-4 border-gray-200 hover:border-gray-300 md:pl-0 md:pt-4 md:pb-0 md:border-l-0 md:border-t-4">
                                    <span class="text-xs text-gray-500 font-semibold tracking-wide uppercase group-hover:text-gray-700">Step {{ $stepNumber }}</span>
                                    <span class="text-sm font-medium">{{ $stepName }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    @endif

    {{-- Flash Messages --}}
    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <p class="text-yellow-800 font-medium">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Help Section for Pro-Se Users --}}
    @if(!$this->isLegalProfessional)
        <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <h4 class="text-blue-800 font-medium mb-1">Getting Started</h4>
                    <p class="text-blue-700 text-sm">
                        Don't worry if you don't have all the information right now. You can save your progress and come back later to add more details. 
                        Your work is automatically saved as you go.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Step Content --}}
    <div class="min-h-[400px]">
        @if($currentStep === 1)
            @livewire('case-creation-wizard.basic-information', [
                'isLegalProfessional' => $this->isLegalProfessional,
                'basicInfo' => $this->basicInfo
            ])
        @elseif($currentStep === 2)
            @livewire('case-creation-wizard.party-management', [
                'isLegalProfessional' => $this->isLegalProfessional,
                'parties' => $this->parties
            ])
        @elseif($currentStep === 3)
            @livewire('case-creation-wizard.key-dates', [
                'isLegalProfessional' => $this->isLegalProfessional,
                'keyDates' => $this->keyDates
            ])
        @elseif($currentStep === 4)
            @livewire('case-creation-wizard.document-upload', [
                'isLegalProfessional' => $this->isLegalProfessional,
                'documents' => $this->documents
            ])
        @elseif($currentStep === 5)
            @livewire('case-creation-wizard.review-confirm', [
                'isLegalProfessional' => $this->isLegalProfessional,
                'basicInformation' => $this->caseData,
                'parties' => $this->parties,
                'keyDates' => $this->keyDates,
                'documents' => $this->documents
            ])
        @endif
    </div>

    {{-- Navigation Footer --}}
    <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-200">
        <div>
            @if($currentStep > 1)
                <button wire:click="previousStep" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </button>
            @endif
        </div>

        <div class="flex items-center space-x-3">
            {{-- Step indicator --}}
            <span class="text-sm text-gray-500">
                Step {{ $currentStep }} of {{ $totalSteps }}
            </span>

            @if($currentStep < $totalSteps)
                <button wire:click="nextStep" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Next
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @else
                <button wire:click="submitWizard" 
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    @if($this->isLegalProfessional)
                        Create Case
                    @else
                        Create My Case
                    @endif
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-900 font-medium">Processing...</span>
        </div>
    </div>
</div>
