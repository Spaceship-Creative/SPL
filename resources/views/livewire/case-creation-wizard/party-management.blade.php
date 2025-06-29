<div>
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Party Management</h2>
        @if($isLegalProfessional)
            <p class="text-gray-600">Add all parties involved in this case.</p>
        @else
            <p class="text-gray-600">Add everyone involved in your case - yourself, the other party, and any witnesses. Don't worry if you don't have all their details right now.</p>
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
                    <h3 class="text-sm font-medium text-blue-800">Understanding Parties</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• <strong>Plaintiff:</strong> The person bringing the case (usually you)</li>
                        <li>• <strong>Defendant:</strong> The person being sued or accused</li>
                        <li>• <strong>Petitioner:</strong> Person requesting court action (family court cases)</li>
                        <li>• <strong>Respondent:</strong> Person responding to a petition</li>
                        <li>• Only include essential contact info - full addresses aren't always needed</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Success/Error Messages --}}
    @if (session()->has('party-success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="ml-3 text-sm text-green-800">{{ session('party-success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('party-error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm text-red-800">{{ session('party-error') }}</p>
            </div>
        </div>
    @endif

    {{-- Existing Parties List --}}
    @if(!empty($parties))
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Current Parties ({{ count($parties) }})
                @if(!$isLegalProfessional)
                    <span class="text-sm font-normal text-gray-500">- People involved in your case</span>
                @endif
            </h3>
            
            <div class="space-y-3">
                @foreach($parties as $party)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $party['name'] }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ in_array($party['type'], ['plaintiff', 'petitioner']) ? 'bg-blue-100 text-blue-800' : 
                                           (in_array($party['type'], ['defendant', 'respondent']) ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ $this->getPartyDisplayType($party['type']) }}
                                    </span>
                                    @if($party['category'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                            {{ $this->getPartyDisplayCategory($party['category']) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                    @if($party['email'])
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                            {{ $party['email'] }}
                                        </div>
                                    @endif
                                    
                                    @if($party['phone'])
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            {{ $party['phone'] }}
                                        </div>
                                    @endif
                                    
                                    @if($party['address'])
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $party['address'] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <button wire:click="removeParty('{{ $party['id'] }}')" 
                                    onclick="return confirm('{{ $isLegalProfessional ? 'Remove this party?' : 'Are you sure you want to remove this person from your case?' }}')"
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

    {{-- Add New Party Form --}}
    <div class="border border-gray-200 rounded-lg p-6 bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ empty($parties) ? 'Add First Party' : 'Add Another Party' }}
            @if(!$isLegalProfessional && empty($parties))
                <span class="text-sm font-normal text-gray-500">- Start by adding yourself</span>
            @endif
        </h3>

        <div class="space-y-6">
            {{-- Party Type and Name Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Party Type --}}
                <div>
                    <label for="newPartyType" class="block text-sm font-medium text-gray-700 mb-2">
                        Party Type
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(What role do they play?)</span>
                        @endif
                    </label>
                    <select id="newPartyType" 
                            wire:model.blur="newPartyType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   @error('newPartyType') border-red-300 @enderror">
                        <option value="">{{ $isLegalProfessional ? 'Select type' : 'Choose party type...' }}</option>
                        @foreach($partyTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('newPartyType')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Party Name --}}
                <div>
                    <label for="newPartyName" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(Person or business name)</span>
                        @endif
                    </label>
                    <input type="text" 
                           id="newPartyName"
                           wire:model.blur="newPartyName" 
                           placeholder="{{ $isLegalProfessional ? 'Enter full name' : 'e.g., John Smith or ABC Company' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  @error('newPartyName') border-red-300 @enderror">
                    @error('newPartyName')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Category --}}
            <div>
                <label for="newPartyCategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Party Category
                    @if(!$isLegalProfessional)
                        <span class="text-xs text-gray-500 font-normal">(Person or organization type)</span>
                    @endif
                </label>
                <select id="newPartyCategory" 
                        wire:model.blur="newPartyCategory"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               @error('newPartyCategory') border-red-300 @enderror">
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('newPartyCategory')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contact Information - Expandable for Pro-Se --}}
            @if($isLegalProfessional)
                {{-- Compact grid for legal professionals --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="newPartyEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               id="newPartyEmail"
                               wire:model.blur="newPartyEmail" 
                               placeholder="email@example.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('newPartyEmail') border-red-300 @enderror">
                        @error('newPartyEmail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="newPartyPhone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" 
                               id="newPartyPhone"
                               wire:model.blur="newPartyPhone" 
                               placeholder="(555) 123-4567"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('newPartyPhone') border-red-300 @enderror">
                        @error('newPartyPhone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="newPartyAddress" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea id="newPartyAddress" 
                              wire:model.blur="newPartyAddress" 
                              rows="2"
                              placeholder="Street address, city, state, zip"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                     @error('newPartyAddress') border-red-300 @enderror"></textarea>
                    @error('newPartyAddress')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @else
                {{-- Expanded form with guidance for pro-se --}}
                <div class="space-y-4">
                    <div>
                        <label for="newPartyEmail" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                            <span class="text-xs text-gray-500 font-normal">(Optional - for court notifications)</span>
                        </label>
                        <input type="email" 
                               id="newPartyEmail"
                               wire:model.blur="newPartyEmail" 
                               placeholder="email@example.com (if known)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('newPartyEmail') border-red-300 @enderror">
                        @error('newPartyEmail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Used for official court communications when available</p>
                    </div>

                    <div>
                        <label for="newPartyPhone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                            <span class="text-xs text-gray-500 font-normal">(Optional - for contact purposes)</span>
                        </label>
                        <input type="tel" 
                               id="newPartyPhone"
                               wire:model.blur="newPartyPhone" 
                               placeholder="(555) 123-4567 (if available)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('newPartyPhone') border-red-300 @enderror">
                        @error('newPartyPhone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="newPartyAddress" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                            <span class="text-xs text-gray-500 font-normal">(Optional - may be needed for service)</span>
                        </label>
                        <textarea id="newPartyAddress" 
                                  wire:model.blur="newPartyAddress" 
                                  rows="3"
                                  placeholder="Street address, city, state, zip (if known)"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                         focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                         @error('newPartyAddress') border-red-300 @enderror"></textarea>
                        @error('newPartyAddress')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
<p class="mt-1 text-xs text-gray-500">
    {{ mb_strlen($newPartyAddress) }}/500 characters. Don't worry if you don't have the full address - you can add it later.
</p>
                    </div>
                </div>
            @endif

            {{-- Add Party Button --}}
            <div class="flex justify-end">
                <button wire:click="addParty"
                        class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium 
                               text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                               focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                    <div wire:loading.remove>
                        {{ $isLegalProfessional ? 'Add Party' : 'Add This Person' }}
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

    {{-- Requirements Summary for Pro-Se Users --}}
    @if(!$isLegalProfessional)
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Before You Continue</h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        Make sure you have at least one person bringing the case (plaintiff/petitioner) and one person being sued (defendant/respondent). 
                        These are the minimum parties required for most legal cases.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
