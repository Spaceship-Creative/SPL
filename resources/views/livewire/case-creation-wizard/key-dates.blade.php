<div>
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Key Dates & Deadlines</h2>
        @if($isLegalProfessional)
            <p class="text-gray-600">Add important dates and deadlines for this case.</p>
        @else
            <p class="text-gray-600">Keep track of important dates so you don't miss any deadlines. Even if you're not sure of exact dates, adding approximate ones can help you stay organized.</p>
        @endif
    </div>

    {{-- Conditional Help Section for Pro-Se Users --}}
    @if(!$isLegalProfessional)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Why Track Dates?</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• <strong>Court dates:</strong> When you need to appear in person</li>
                        <li>• <strong>Filing deadlines:</strong> When paperwork must be submitted</li>
                        <li>• <strong>Response deadlines:</strong> When the other party must respond</li>
                        <li>• <strong>Missing deadlines can hurt your case</strong> - even if dates change later</li>
                        <li>• You can always add more dates or update them as your case progresses</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Success/Warning Messages --}}
    @if (session()->has('date-success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="ml-3 text-sm text-green-800">{{ session('date-success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('date-warning'))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="ml-3 text-sm text-yellow-800">{{ session('date-warning') }}</p>
            </div>
        </div>
    @endif

    {{-- Existing Key Dates List --}}
    @if(!empty($keyDates))
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Your Important Dates ({{ count($keyDates) }})
                @if(!$isLegalProfessional)
                    <span class="text-sm font-normal text-gray-500">- Sorted by priority and date</span>
                @endif
            </h3>
            
            <div class="space-y-3">
                @foreach($keyDates as $keyDate)
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $keyDate['title'] }}</h4>
                                    
                                    {{-- Priority Badge --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $keyDate['priority'] === 'critical' ? 'bg-red-100 text-red-800' : 
                                           ($keyDate['priority'] === 'high' ? 'bg-orange-100 text-orange-800' : 
                                            ($keyDate['priority'] === 'medium' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $this->getPriorityDisplayName($keyDate['priority']) }}
                                    </span>
                                    
                                    {{-- Date Type Badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $this->getDateDisplayType($keyDate['type']) }}
                                    </span>
                                </div>
                                
                                {{-- Date and Time Display --}}
                                <div class="mb-2">
                                    <div class="flex items-center text-sm text-gray-900">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">{{ $this->formatDate($keyDate['date'], $keyDate['time']) }}</span>
                                        <span class="ml-2 text-gray-500">({{ $this->getDaysUntil($keyDate['date']) }})</span>
                                    </div>
                                </div>
                                
                                {{-- Description --}}
                                @if($keyDate['description'])
                                    <p class="text-sm text-gray-600">{{ $keyDate['description'] }}</p>
                                @endif
                            </div>
                            
                            <button 
                                wire:click="removeKeyDate('{{ $keyDate['id'] }}')" 
                                wire:confirm="{{ $isLegalProfessional 
                                    ? 'Remove this date?' 
                                    : 'Are you sure you want to remove this important date?' 
                                }}"
                                class="ml-4 text-red-600 hover:text-red-800 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Add New Date Form --}}
    <div class="border border-gray-200 rounded-lg p-6 bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ empty($keyDates) ? 'Add Your First Important Date' : 'Add Another Date' }}
            @if(!$isLegalProfessional && empty($keyDates))
                <span class="text-sm font-normal text-gray-500">- Start with the most important deadline</span>
            @endif
        </h3>

        <div class="space-y-6">
            {{-- Title and Type Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Date Title
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(What is this date for?)</span>
                        @endif
                    </label>
                    <input type="text" 
                           id="title"
                           wire:model.blur="title" 
                           placeholder="{{ $isLegalProfessional ? 'Enter date title' : 'e.g., First court hearing, Response deadline' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  @error('title') border-red-300 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-xs text-gray-500">
                        {{ mb_strlen($title) }}/255 characters
                    </div>

                {{-- Date Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Date Type
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(What kind of date is this?)</span>
                        @endif
                    </label>
                    <select id="type" 
                            wire:model.blur="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   @error('type') border-red-300 @enderror">
                        <option value="">{{ $isLegalProfessional ? 'Select type' : 'Choose date type...' }}</option>
                        @foreach($dateTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Date and Time Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Date --}}
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(When is this happening?)</span>
                        @endif
                    </label>
                    <input type="date" 
                           id="date"
                           wire:model.blur="date" 
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  @error('date') border-red-300 @enderror">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(!$isLegalProfessional && !$errors->has('date'))
                        <p class="mt-1 text-xs text-gray-500">Must be today or a future date</p>
                    @endif
                </div>

                {{-- Time (Optional) --}}
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700 mb-2">
                        Time
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(Optional - if you know the specific time)</span>
                        @endif
                    </label>
                    <input type="time" 
                           id="time"
                           wire:model.blur="time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  @error('time') border-red-300 @enderror">
                    @error('time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if(!$isLegalProfessional && !$errors->has('time'))
                        <p class="mt-1 text-xs text-gray-500">Leave blank if time is unknown</p>
                    @endif
                </div>
            </div>

            {{-- Priority --}}
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    Priority Level
                    <span class="text-red-500">*</span>
                    @if(!$isLegalProfessional)
                        <span class="text-xs text-gray-500 font-normal">(How important is this date?)</span>
                    @endif
                </label>
                <select id="priority" 
                        wire:model.blur="priority"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               @error('priority') border-red-300 @enderror">
                    @foreach($priorityOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if(!$isLegalProfessional && !$errors->has('priority'))
                    <div class="mt-2 p-3 bg-gray-50 rounded-md">
                        <p class="text-xs text-gray-600 font-medium mb-1">Priority Guide:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• <strong>Must Not Miss:</strong> Court deadlines, filing deadlines</li>
                            <li>• <strong>Very Important:</strong> Court hearings, depositions</li>
                            <li>• <strong>Important:</strong> Mediation, settlement conferences</li>
                            <li>• <strong>Nice to Remember:</strong> Follow-up calls, document reviews</li>
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes/Description
                    @if(!$isLegalProfessional)
                        <span class="text-xs text-gray-500 font-normal">(Optional - any additional details)</span>
                    @endif
                </label>
                <textarea id="description" 
                          wire:model.blur="description" 
                          rows="{{ $isLegalProfessional ? '3' : '4' }}"
                          placeholder="{{ $isLegalProfessional ? 
                              'Additional notes...' : 
                              'e.g., Bring all lease documents, arrive 15 minutes early, located at courthouse room 204' }}"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                 @error('description') border-red-300 @enderror"></textarea>
<div class="mt-1 flex justify-between text-xs text-gray-500">
    <span>{{ mb_strlen($description) }}/500 characters</span>
    @if(!$isLegalProfessional)
        <span>Include preparation notes, documents to bring, location details</span>
    @endif
</div>
                <div class="mt-1 flex justify-between text-xs text-gray-500">
                    <span>{{ mb_strlen($description) }}/500 characters</span>
                    @if(!$isLegalProfessional)
                        <span>Include preparation notes, documents to bring, location details</span>
                    @endif
                </div>
            </div>

            {{-- Add Date Button --}}
            <div class="flex justify-end">
                <button wire:click="addKeyDate"
                        class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium 
                               text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                               focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                    <div wire:loading.remove>
                        {{ $isLegalProfessional ? 'Add Date' : 'Add This Date' }}
                    </div>
                    <div wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Adding...
                    </div>
                </button>
            </div>
        </div>
    </div>

    {{-- Validation Summary --}}
    @if($errors->any())
        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following:</h3>
                    <ul class="mt-2 text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Encouragement Section for Pro-Se Users --}}
    @if(!$isLegalProfessional)
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">You're Doing Great!</h3>
                    <p class="mt-1 text-sm text-green-700">
                        Even if you don't have many dates yet, that's completely normal. You can always come back and add more dates as your case progresses. 
                        The important thing is to start tracking what you know now.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
