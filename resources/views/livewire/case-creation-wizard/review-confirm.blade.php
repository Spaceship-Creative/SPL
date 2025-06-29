<div class="space-y-6">
    {{-- Header Section --}}
    <div class="text-center mb-8">
        @if($isLegalProfessional)
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Review Case Details</h2>
            <p class="text-gray-600">Please review the case information below before proceeding with creation.</p>
        @else
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Review Your Case</h2>
            <p class="text-gray-600">Take a moment to review all the information you've entered. You can make changes by clicking the "Edit" buttons.</p>
        @endif
    </div>

    {{-- Validation Warnings --}}
    @if(!empty($this->validationWarnings))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                    <h4 class="text-yellow-800 font-medium mb-1">
                        @if($isLegalProfessional)
                            Missing Required Information
                        @else
                            Let's Complete Your Case Information
                        @endif
                    </h4>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        @foreach($this->validationWarnings as $warning)
                            <li>• {{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Data Completeness Indicator --}}
    @if($this->isDataComplete)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="ml-3 text-sm text-green-800 font-medium">
                    @if($isLegalProfessional)
                        All required information is complete. Ready to create case.
                    @else
                        Great! Your case information looks complete and ready to submit.
                    @endif
                </p>
            </div>
        </div>
    @endif

    {{-- Basic Information Review --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Basic Information
            </h3>
            <button wire:click="editStep(1)" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-700">Case Name</label>
                <p class="text-gray-900 mt-1">{{ $this->basicInformation['name'] ?: 'Not provided' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-700">Case Number</label>
                <p class="text-gray-900 mt-1">
                    {{ $this->basicInformation['case_number'] }}
                    @if(!$isLegalProfessional && $this->basicInformation['case_number'] === 'Not specified')
                        <span class="text-gray-500 text-sm">(will be assigned by court)</span>
                    @endif
                </p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-700">Case Type</label>
                <p class="text-gray-900 mt-1">{{ ucfirst(str_replace('_', ' ', $this->basicInformation['type'])) ?: 'Not selected' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-700">Jurisdiction</label>
                <p class="text-gray-900 mt-1">{{ $this->basicInformation['jurisdiction'] ?: 'Not provided' }}</p>
            </div>
            
            <div>
                <label class="text-sm font-medium text-gray-700">Venue</label>
                <p class="text-gray-900 mt-1">{{ $this->basicInformation['venue'] ?: 'Not provided' }}</p>
            </div>
        </div>
        
        @if($this->basicInformation['description'])
            <div class="mt-6">
                <label class="text-sm font-medium text-gray-700">Case Description</label>
                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                    <p class="text-gray-900">{{ $this->basicInformation['description'] }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Parties Review --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Case Parties
            </h3>
            <button wire:click="editStep(2)" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </button>
        </div>
        
        @php $totalParties = count($this->organizedParties['plaintiffs']) + count($this->organizedParties['defendants']) + count($this->organizedParties['attorneys']) + count($this->organizedParties['judges']) + count($this->organizedParties['witnesses']) + count($this->organizedParties['others']); @endphp
        
        @if($totalParties === 0)
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No parties added yet</p>
                @if(!$isLegalProfessional)
                    <p class="text-gray-400 text-xs mt-1">Add people or organizations involved in your case</p>
                @endif
            </div>
        @else
            <div class="space-y-6">
                {{-- Plaintiffs --}}
                @if(!empty($this->organizedParties['plaintiffs']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                {{ count($this->organizedParties['plaintiffs']) }}
                            </span>
                            Plaintiff{{ count($this->organizedParties['plaintiffs']) > 1 ? 's' : '' }}
                        </h4>
                        <div class="space-y-2">
                            @foreach($this->organizedParties['plaintiffs'] as $party)
                                <div class="flex items-start p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $party['name'] }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($party['category'] ?? 'individual') }}</p>
                                        @if(!empty($party['email']) || !empty($party['phone']) || !empty($party['address']))
                                            <div class="mt-2 text-xs text-gray-500 space-y-1">
                                                @if(!empty($party['email']))
                                                    <div class="flex items-center">
                                                        <span>📧 {{ $party['email'] }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($party['phone']))
                                                    <div class="flex items-center">
                                                        <span>📞 {{ $party['phone'] }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($party['address']))
                                                    <div class="flex items-start">
                                                        <span>📍 {{ $party['address'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Defendants --}}
                @if(!empty($this->organizedParties['defendants']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                {{ count($this->organizedParties['defendants']) }}
                            </span>
                            Defendant{{ count($this->organizedParties['defendants']) > 1 ? 's' : '' }}
                        </h4>
                        <div class="space-y-2">
                            @foreach($this->organizedParties['defendants'] as $party)
                                <div class="flex items-start p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $party['name'] }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($party['category'] ?? 'individual') }}</p>
                                        @if(!empty($party['email']) || !empty($party['phone']) || !empty($party['address']))
                                            <div class="mt-2 text-xs text-gray-500 space-y-1">
                                                @if(!empty($party['email']))
                                                    <div class="flex items-center">
                                                        <span>📧 {{ $party['email'] }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($party['phone']))
                                                    <div class="flex items-center">
                                                        <span>📞 {{ $party['phone'] }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($party['address']))
                                                    <div class="flex items-start">
                                                        <span>📍 {{ $party['address'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Other Party Types --}}
                @php
                    $partyTypeStyles = [
                        'blue' => ['badge' => 'bg-blue-100 text-blue-800', 'card' => 'bg-blue-50 border-blue-200'],
                        'purple' => ['badge' => 'bg-purple-100 text-purple-800', 'card' => 'bg-purple-50 border-purple-200'],
                        'yellow' => ['badge' => 'bg-yellow-100 text-yellow-800', 'card' => 'bg-yellow-50 border-yellow-200'],
                        'gray' => ['badge' => 'bg-gray-100 text-gray-800', 'card' => 'bg-gray-50 border-gray-200'],
                    ];
                @endphp
                @foreach(['attorneys' => ['title' => 'Attorneys', 'color' => 'blue'], 'judges' => ['title' => 'Judges', 'color' => 'purple'], 'witnesses' => ['title' => 'Witnesses', 'color' => 'yellow'], 'others' => ['title' => 'Other Parties', 'color' => 'gray']] as $type => $config)
                    @if(!empty($this->organizedParties[$type]))
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mr-2 {{ $partyTypeStyles[$config['color']]['badge'] }}">
                                    {{ count($this->organizedParties[$type]) }}
                                </span>
                                {{ $config['title'] }}
                            </h4>
                            <div class="space-y-2">
                                @foreach($this->organizedParties[$type] as $party)
                                    <div class="flex items-start p-3 border rounded-lg {{ $partyTypeStyles[$config['color']]['card'] }}">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $party['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ ucfirst($party['category'] ?? 'individual') }}</p>
                                            @if(!empty($party['email']) || !empty($party['phone']) || !empty($party['address']))
                                                <div class="mt-2 text-xs text-gray-500 space-y-1">
                                                    @if(!empty($party['email']))
                                                        <div>📧 {{ $party['email'] }}</div>
                                                    @endif
                                                    @if(!empty($party['phone']))
                                                        <div>📞 {{ $party['phone'] }}</div>
                                                    @endif
                                                    @if(!empty($party['address']))
                                                        <div>📍 {{ $party['address'] }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Key Dates Review --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Key Dates & Deadlines
            </h3>
            <button wire:click="editStep(3)" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </button>
        </div>

        @php $totalDates = count($this->organizedKeyDates['high_priority']) + count($this->organizedKeyDates['medium_priority']) + count($this->organizedKeyDates['low_priority']); @endphp

        @if($totalDates === 0)
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No key dates added yet</p>
                @if(!$isLegalProfessional)
                    <p class="text-gray-400 text-xs mt-1">Add important deadlines and court dates</p>
                @endif
            </div>
        @else
            <div class="space-y-6">
                {{-- High Priority Dates --}}
                @if(!empty($this->organizedKeyDates['high_priority']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                {{ count($this->organizedKeyDates['high_priority']) }}
                            </span>
                            High Priority
                        </h4>
                        <div class="space-y-2">
                            @foreach($this->organizedKeyDates['high_priority'] as $date)
                                <div class="flex items-start p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $date['title'] }}</p>
                                        <div class="flex items-center text-sm text-gray-600 mt-1">
                                            <span>📅 {{ \Carbon\Carbon::parse($date['date'])->format('M j, Y') }}</span>
                                            @if(!empty($date['time']))
                                                <span class="ml-3">🕐 {{ \Carbon\Carbon::parse($date['time'])->format('g:i A') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ ucfirst($date['type'] ?? 'deadline') }}</p>
                                        @if(!empty($date['description']))
                                            <p class="text-sm text-gray-600 mt-2">{{ $date['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Medium Priority Dates --}}
                @if(!empty($this->organizedKeyDates['medium_priority']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-2">
                                {{ count($this->organizedKeyDates['medium_priority']) }}
                            </span>
                            Medium Priority
                        </h4>
                        <div class="space-y-2">
                            @foreach($this->organizedKeyDates['medium_priority'] as $date)
                                <div class="flex items-start p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $date['title'] }}</p>
                                        <div class="flex items-center text-sm text-gray-600 mt-1">
                                            <span>📅 {{ \Carbon\Carbon::parse($date['date'])->format('M j, Y') }}</span>
                                            @if(!empty($date['time']))
                                                <span class="ml-3">🕐 {{ \Carbon\Carbon::parse($date['time'])->format('g:i A') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ ucfirst($date['type'] ?? 'deadline') }}</p>
                                        @if(!empty($date['description']))
                                            <p class="text-sm text-gray-600 mt-2">{{ $date['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Low Priority Dates --}}
                @if(!empty($this->organizedKeyDates['low_priority']))
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                {{ count($this->organizedKeyDates['low_priority']) }}
                            </span>
                            Low Priority
                        </h4>
                        <div class="space-y-2">
                            @foreach($this->organizedKeyDates['low_priority'] as $date)
                                <div class="flex items-start p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $date['title'] }}</p>
                                        <div class="flex items-center text-sm text-gray-600 mt-1">
                                            <span>📅 {{ \Carbon\Carbon::parse($date['date'])->format('M j, Y') }}</span>
                                            @if(!empty($date['time']))
                                                <span class="ml-3">🕐 {{ \Carbon\Carbon::parse($date['time'])->format('g:i A') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ ucfirst($date['type'] ?? 'deadline') }}</p>
                                        @if(!empty($date['description']))
                                            <p class="text-sm text-gray-600 mt-2">{{ $date['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Documents Review --}}
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Case Documents
            </h3>
            <button wire:click="editStep(4)" 
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </button>
        </div>

        @php $totalDocs = count($this->organizedDocuments['complaints']) + count($this->organizedDocuments['motions']) + count($this->organizedDocuments['orders']) + count($this->organizedDocuments['evidence']) + count($this->organizedDocuments['correspondence']) + count($this->organizedDocuments['other']); @endphp

        @if($totalDocs === 0)
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No documents added yet</p>
                @if(!$isLegalProfessional)
                    <p class="text-gray-400 text-xs mt-1">Document uploads will be available after case creation</p>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach(['complaints' => 'Complaints', 'motions' => 'Motions', 'orders' => 'Orders', 'evidence' => 'Evidence', 'correspondence' => 'Correspondence', 'other' => 'Other Documents'] as $category => $title)
                    @if(!empty($this->organizedDocuments[$category]))
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ $title }} ({{ count($this->organizedDocuments[$category]) }})</h4>
                            <div class="space-y-2">
                                @foreach($this->organizedDocuments[$category] as $doc)
                                    <div class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                        <div class="flex-shrink-0 mr-3">
                                            <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $doc['title'] ?? 'Untitled Document' }}</p>
                                            @if(!empty($doc['description']))
                                                <p class="text-sm text-gray-600 mt-1">{{ $doc['description'] }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-1">{{ ucfirst($doc['category'] ?? 'document') }} placeholder</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Final Summary --}}
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Case Summary
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="bg-white rounded-lg p-4">
                <div class="text-2xl font-bold text-blue-600">1</div>
                <div class="text-sm text-gray-600">Case</div>
            </div>
            <div class="bg-white rounded-lg p-4">
                <div class="text-2xl font-bold text-green-600">{{ $totalParties }}</div>
                <div class="text-sm text-gray-600">{{ $totalParties === 1 ? 'Party' : 'Parties' }}</div>
            </div>
            <div class="bg-white rounded-lg p-4">
                <div class="text-2xl font-bold text-purple-600">{{ $totalDates }}</div>
                <div class="text-sm text-gray-600">{{ $totalDates === 1 ? 'Date' : 'Dates' }}</div>
            </div>
            <div class="bg-white rounded-lg p-4">
                <div class="text-2xl font-bold text-orange-600">{{ $totalDocs }}</div>
                <div class="text-sm text-gray-600">{{ $totalDocs === 1 ? 'Document' : 'Documents' }}</div>
            </div>
        </div>

        @if(!$isLegalProfessional)
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="text-blue-800 font-medium mb-2">What happens next?</h4>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>✓ Your case will be created and saved securely</li>
                    <li>✓ You can upload documents and add more details later</li>
                    <li>✓ Use the AI assistant to analyze your case documents</li>
                    <li>✓ Generate legal filings with AI assistance</li>
                </ul>
            </div>
        @endif
    </div>
</div>
