<div class="max-w-6xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    {{-- Header Section with User Type Indicator --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Create New Case</h1>
            <div class="flex items-center space-x-2">
                @if($this->isLegalProfessional)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Legal Professional
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Pro-Se Litigant
                    </span>
                @endif
            </div>
        </div>
        
        {{-- Conditional Welcome Message --}}
        @if($this->isLegalProfessional)
            <p class="text-gray-600">Streamlined case creation for legal professionals. Enter case details efficiently.</p>
        @else
            <p class="text-gray-600">We'll guide you through creating your case step by step. Take your time and use the help text as needed.</p>
        @endif
    </div>

    {{-- Progress Bar --}}
    <div class="mb-8">
        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
            <span>Step {{ $currentStep }} of {{ $totalSteps }}</span>
            <span>{{ round(($currentStep / $totalSteps) * 100) }}% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                 style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
        </div>
    </div>

    {{-- Step Navigation (Horizontal for Legal Professionals, Vertical for Pro-Se) --}}
    @if($this->isLegalProfessional)
        {{-- Compact horizontal navigation for legal professionals --}}
        <div class="flex justify-center mb-6">
            <nav class="flex space-x-8">
                @foreach(['Basic Information', 'Parties', 'Key Dates', 'Documents', 'Review'] as $index => $stepName)
                    <button wire:click="goToStep({{ $index + 1 }})" 
                            class="flex items-center text-sm font-medium transition-colors
                                   {{ $currentStep == $index + 1 ? 'text-blue-600' : 
                                      ($currentStep > $index + 1 ? 'text-green-600' : 'text-gray-400') }}">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full border-2 mr-2
                                     {{ $currentStep == $index + 1 ? 'border-blue-600 bg-blue-600 text-white' : 
                                        ($currentStep > $index + 1 ? 'border-green-600 bg-green-600 text-white' : 'border-gray-300') }}">
                            @if($currentStep > $index + 1)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </span>
                        {{ $stepName }}
                    </button>
                @endforeach
            </nav>
        </div>
    @else
        {{-- Detailed navigation for pro-se users --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-gray-900 mb-3">Case Creation Steps:</h3>
            <div class="space-y-2">
                @foreach([
                    'Basic Information' => 'Enter your case name, type, and basic details',
                    'Parties' => 'Add all people or organizations involved in your case',
                    'Key Dates' => 'Record important deadlines and court dates',
                    'Documents' => 'Upload or note important case documents',
                    'Review & Confirm' => 'Review all your information and create your case'
                ] as $index => $stepInfo)
                    @php $stepNum = $loop->iteration; @endphp
                    <div class="flex items-start space-x-3">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium
                                     {{ $currentStep == $stepNum ? 'bg-blue-600 text-white' : 
                                        ($currentStep > $stepNum ? 'bg-green-600 text-white' : 'bg-gray-300 text-gray-600') }}">
                            @if($currentStep > $stepNum)
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                {{ $stepNum }}
                            @endif
                        </span>
                        <div class="flex-1">
                            <h4 class="text-sm font-medium {{ $currentStep == $stepNum ? 'text-blue-600' : 'text-gray-900' }}">
                                {{ $index }}
                            </h4>
                            <p class="text-xs text-gray-500">{{ $stepInfo }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Error/Success Messages --}}
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="ml-3 text-sm text-yellow-800">{{ session('warning') }}</p>
            </div>
        </div>
    @endif

    {{-- Main Content Area --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
        @if($currentStep == 1)
            <livewire:case-creation-wizard.basic-information 
                :user-type="$this->userType"
                :is-legal-professional="$this->isLegalProfessional" />
        @elseif($currentStep == 2)
            <livewire:case-creation-wizard.party-management 
                :user-type="$this->userType"
                :is-legal-professional="$this->isLegalProfessional" />
        @elseif($currentStep == 3)
            <livewire:case-creation-wizard.key-dates 
                :user-type="$this->userType"
                :is-legal-professional="$this->isLegalProfessional" />
        @elseif($currentStep == 4)
            <livewire:case-creation-wizard.document-upload 
                :user-type="$this->userType"
                :is-legal-professional="$this->isLegalProfessional" />
        @elseif($currentStep == 5)
            <livewire:case-creation-wizard.review-confirm 
                :case-data="$caseData"
                :user-type="$this->userType"
                :is-legal-professional="$this->isLegalProfessional" />
        @endif
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex justify-between items-center">
        <button wire:click="previousStep" 
                @if($currentStep == 1) disabled @endif
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 
                       bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 
                       focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Previous
        </button>

        <div class="flex space-x-3">
            @if($currentStep < $totalSteps)
                <button wire:click="nextStep"
                        class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium 
                               text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                               focus:ring-blue-500">
                    Continue
                    <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            @else
                <button wire:click="submitWizard"
                        class="px-6 py-2 bg-green-600 border border-transparent rounded-md text-sm font-medium 
                               text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                               focus:ring-green-500">
                    @if($this->isLegalProfessional)
                        Create Case
                    @else
                        Create My Case
                    @endif
                    <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Conditional Help Section for Pro-Se Users --}}
    @if($this->isProSe)
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Need Help?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Don't worry if you're not sure about something. You can:</p>
                        <ul class="mt-2 space-y-1">
                            <li>• Leave optional fields blank and return later</li>
                            <li>• Use approximate information if exact details aren't available</li>
                            <li>• Contact support if you need assistance</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
