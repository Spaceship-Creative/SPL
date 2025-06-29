<div>
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Basic Case Information</h2>
        @if($isLegalProfessional)
            <p class="text-gray-600">Enter the fundamental details for this case.</p>
        @else
            <p class="text-gray-600">Let's start with the basic details about your legal case. Don't worry - you can change these later if needed.</p>
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
                    <h3 class="text-sm font-medium text-blue-800">Getting Started Tips</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• Choose a descriptive case name (e.g., "Landlord Security Deposit Dispute")</li>
                        <li>• Case number is optional - many pro-se cases don't have one yet</li>
                        <li>• Select the case type that best matches your situation</li>
                        <li>• Be as detailed as possible in your case description</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Fields --}}
    <div class="space-y-6">
        {{-- Case Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Case Name
                <span class="text-red-500">*</span>
                @if(!$isLegalProfessional)
                    <span class="text-xs text-gray-500 font-normal">(Give your case a descriptive name)</span>
                @endif
            </label>
            <input type="text" 
                   id="name"
                   wire:model.blur="name" 
                   placeholder="{{ $isLegalProfessional ? 'Enter case name' : 'e.g., Landlord Security Deposit Dispute' }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          @error('name') border-red-300 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="mt-1 text-xs text-gray-500">
                {{ mb_strlen($name) }}/255 characters
            </div>
        </div>

        {{-- Case Number - Conditional Display --}}
        <div>
            <label for="caseNumber" class="block text-sm font-medium text-gray-700 mb-2">
                Case Number
                @if($isLegalProfessional)
                    <span class="text-red-500">*</span>
                @else
                    <span class="text-xs text-gray-500 font-normal">(Optional - leave blank if you don't have one)</span>
                @endif
            </label>
            <input type="text" 
                   id="caseNumber"
                   wire:model.blur="caseNumber" 
                   placeholder="{{ $isLegalProfessional ? 'Enter case number' : 'e.g., CV-2024-001234 (if available)' }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          @error('caseNumber') border-red-300 @enderror">
            @error('caseNumber')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(!$isLegalProfessional && !$errors->has('caseNumber'))
                <p class="mt-1 text-xs text-gray-500">Many pro-se cases don't have a case number initially. The court will assign one when you file.</p>
            @endif
        </div>

        {{-- Case Type --}}
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                Case Type
                <span class="text-red-500">*</span>
                @if(!$isLegalProfessional)
                    <span class="text-xs text-gray-500 font-normal">(Choose the option that best describes your situation)</span>
                @endif
            </label>
            <select id="type" 
                    wire:model.blur="type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           @error('type') border-red-300 @enderror">
                <option value="">{{ $isLegalProfessional ? 'Select case type' : 'Choose your case type...' }}</option>
                @foreach($caseTypes as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Jurisdiction --}}
        <div>
            <label for="jurisdiction" class="block text-sm font-medium text-gray-700 mb-2">
                Jurisdiction
                <span class="text-red-500">*</span>
                @if(!$isLegalProfessional)
                    <span class="text-xs text-gray-500 font-normal">(Which court system will handle your case)</span>
                @endif
            </label>
            <select id="jurisdiction" 
                    wire:model.blur="jurisdiction"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           @error('jurisdiction') border-red-300 @enderror">
                <option value="">{{ $isLegalProfessional ? 'Select jurisdiction' : 'Choose jurisdiction...' }}</option>
                @foreach($jurisdictions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('jurisdiction')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(!$isLegalProfessional && !$errors->has('jurisdiction'))
                <div class="mt-2 p-3 bg-gray-50 rounded-md">
                    <p class="text-xs text-gray-600 font-medium mb-1">Not sure which to choose?</p>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• <strong>Federal Court:</strong> Cases involving federal laws or parties from different states</li>
                        <li>• <strong>State Court:</strong> Most common for disputes within your state</li>
                        <li>• <strong>Local/Municipal:</strong> Traffic tickets, small claims, local ordinances</li>
                    </ul>
                </div>
            @endif
        </div>

        {{-- Venue --}}
        <div>
            <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">
                Venue/Court
                <span class="text-red-500">*</span>
                @if(!$isLegalProfessional)
                    <span class="text-xs text-gray-500 font-normal">(The specific court location)</span>
                @endif
            </label>
            <input type="text" 
                   id="venue"
                   wire:model.blur="venue" 
                   placeholder="{{ $isLegalProfessional ? 'Enter court/venue' : 'e.g., Superior Court of [Your County]' }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                          @error('venue') border-red-300 @enderror">
            @error('venue')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if(!$isLegalProfessional && !$errors->has('venue'))
                <p class="mt-1 text-xs text-gray-500">Include the specific court name and location where your case will be heard.</p>
            @endif
        </div>

        {{-- Case Description --}}
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Case Description
                <span class="text-red-500">*</span>
                @if(!$isLegalProfessional)
                    <span class="text-xs text-gray-500 font-normal">(Briefly explain what happened and what you're seeking)</span>
                @endif
            </label>
            <textarea id="description" 
                      wire:model.blur="description" 
                      rows="{{ $isLegalProfessional ? '4' : '6' }}"
                      placeholder="{{ $isLegalProfessional ? 
                          'Enter case description...' : 
                          'Example: My landlord is refusing to return my $1,500 security deposit after I moved out, claiming damages that were normal wear and tear. I have photos showing the good condition of the apartment when I left. I am seeking the return of my full security deposit plus interest as required by state law.' }}"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                             focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                             @error('description') border-red-300 @enderror"></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="mt-1 flex justify-between text-xs text-gray-500">
-                <span>{{ strlen($description) }}/1000 characters</span>
+                <span>{{ mb_strlen($description) }}/1000 characters</span>
                 @if(!$isLegalProfessional)
                     <span>Include key facts, what you want, and any relevant laws</span>
                 @endif
             </div>
        </div>
    </div>

    {{-- Summary Section for Legal Professionals --}}
    @if($isLegalProfessional && ($name || $type || $jurisdiction))
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Case Summary</h3>
            <div class="text-sm text-gray-600 space-y-1">
                @if($name)<p><strong>Name:</strong> {{ $name }}</p>@endif
                @if($caseNumber)<p><strong>Number:</strong> {{ $caseNumber }}</p>@endif
                @if($type)<p><strong>Type:</strong> {{ $caseTypes[$type] ?? $type }}</p>@endif
                @if($jurisdiction)<p><strong>Jurisdiction:</strong> {{ $jurisdictions[$jurisdiction] ?? $jurisdiction }}</p>@endif
                @if($venue)<p><strong>Venue:</strong> {{ $venue }}</p>@endif
            </div>
        </div>
    @endif

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
</div>
