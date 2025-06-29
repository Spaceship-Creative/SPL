<div>
    {{-- Header Section --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Document Management</h2>
        @if($isLegalProfessional)
            <p class="text-gray-600">Organize and track case documents. File uploads will be available in a future update.</p>
        @else
            <p class="text-gray-600">Keep track of all your important documents in one place. For now, you can create placeholders for documents you have or need - actual file uploads are coming soon!</p>
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
                    <h3 class="text-sm font-medium text-blue-800">Why Track Documents?</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• <strong>Stay organized:</strong> Know what documents you have and need</li>
                        <li>• <strong>Track deadlines:</strong> Remember when documents are due</li>
                        <li>• <strong>Build your case:</strong> Organize evidence and supporting materials</li>
                        <li>• <strong>Court requirements:</strong> Make sure you have everything for hearings</li>
                        <li>• File uploads are coming soon - for now, just track what you have</li>
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Success/Error Messages --}}
    @if (session()->has('document-success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="ml-3 text-sm text-green-800">{{ session('document-success') }}</p>
            </div>
        </div>
    @endif

    {{-- Coming Soon Notice --}}
    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex">
            <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">File Uploads Coming Soon</h3>
                <p class="mt-1 text-sm text-yellow-700">
                    For now, you can create placeholders to track your documents. Actual file upload functionality will be available in a future update. 
                    This helps you stay organized and ensures you don't forget any important documents.
                </p>
            </div>
        </div>
    </div>

    {{-- Existing Documents List --}}
    @if(!empty($documents))
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Your Document List ({{ count($documents) }})
                @if(!$isLegalProfessional)
                    <span class="text-sm font-normal text-gray-500">- Documents you have or need</span>
                @endif
            </h3>
            
            <div class="space-y-3">
                @foreach($documents as $document)
                    <div class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $document['title'] }}</h4>
                                    
                                    {{-- Document Type Badge --}}
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $this->getDocumentDisplayType($document['type']) }}
                                    </span>
                                    
                                    {{-- Category Badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $this->getDocumentDisplayCategory($document['category']) }}
                                    </span>

                                    {{-- Status Badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $document['status'] === 'placeholder' ? 'To Upload' : ucfirst($document['status']) }}
                                    </span>
                                </div>
                                
                                {{-- Dates --}}
                                @if($document['received_date'] || $document['due_date'])
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2 text-sm text-gray-600">
                                        @if($document['received_date'])
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>Received: {{ $this->formatDate($document['received_date']) }}</span>
                                            </div>
                                        @endif
                                        
                                        @if($document['due_date'])
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Due: {{ $this->formatDate($document['due_date']) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                {{-- Description --}}
                                @if($document['description'])
                                    <p class="text-sm text-gray-600">{{ $document['description'] }}</p>
                                @endif
                            </div>
                            
                            <button wire:click="removeDocumentPlaceholder('{{ $document['id'] }}')" 
                                    onclick="return confirm('{{ $isLegalProfessional ? 'Remove this document?' : 'Are you sure you want to remove this document from your list?' }}')"
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

    {{-- Add New Document Form --}}
    <div class="border border-gray-200 rounded-lg p-6 bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ empty($documents) ? 'Add Your First Document' : 'Add Another Document' }}
            @if(!$isLegalProfessional && empty($documents))
                <span class="text-sm font-normal text-gray-500">- Keep track of what you have</span>
            @endif
        </h3>

        <div class="space-y-6">
            {{-- Title and Type Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Document Title
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(What is this document?)</span>
                        @endif
                    </label>
                    <input type="text" 
                           id="title"
                           wire:model.blur="title" 
                           placeholder="{{ $isLegalProfessional ? 'Enter document title' : 'e.g., Lease Agreement, Court Summons, Photos of Damage' }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                  @error('title') border-red-300 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-xs text-gray-500">
                        {{ mb_strlen($title) }}/255 characters
                    </div>
                </div>

                {{-- Document Type --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Document Type
                        <span class="text-red-500">*</span>
                        @if(!$isLegalProfessional)
                            <span class="text-xs text-gray-500 font-normal">(What kind of document is this?)</span>
                        @endif
                    </label>
                    <select id="type" 
                            wire:model.blur="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                   @error('type') border-red-300 @enderror">
                        <option value="">{{ $isLegalProfessional ? 'Select type' : 'Choose document type...' }}</option>
                        @foreach($documentTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Category --}}
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                    Document Category
                    <span class="text-red-500">*</span>
                    @if(!$isLegalProfessional)
                        <span class="text-xs text-gray-500 font-normal">(Where did this document come from?)</span>
                    @endif
                </label>
                <select id="category" 
                        wire:model.blur="category"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               @error('category') border-red-300 @enderror">
                    <option value="">{{ $isLegalProfessional ? 'Select category' : 'Choose category...' }}</option>
                    @foreach($documentCategories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if(!$isLegalProfessional && !$errors->has('category'))
                    <div class="mt-2 p-3 bg-gray-50 rounded-md">
                        <p class="text-xs text-gray-600 font-medium mb-1">Category Guide:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• <strong>My Documents:</strong> Things you created or already have</li>
                            <li>• <strong>From Court:</strong> Official papers sent by the court</li>
                            <li>• <strong>From Other Party:</strong> Documents they sent you</li>
                            <li>• <strong>Evidence:</strong> Photos, receipts, proof for your case</li>
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Dates Row --}}
            @if($isLegalProfessional)
                {{-- Compact date fields for legal professionals --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="receivedDate" class="block text-sm font-medium text-gray-700 mb-2">Received Date</label>
                        <input type="date" 
                               id="receivedDate"
                               wire:model.blur="receivedDate" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('receivedDate') border-red-300 @enderror">
                        @error('receivedDate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" 
                               id="dueDate"
                               wire:model.blur="dueDate" 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('dueDate') border-red-300 @enderror">
                        @error('dueDate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @else
                {{-- Expanded date fields with guidance for pro-se --}}
                <div class="space-y-4">
                    <div>
                        <label for="receivedDate" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Received
                            <span class="text-xs text-gray-500 font-normal">(Optional - when you got this document)</span>
                        </label>
                        <input type="date" 
                               id="receivedDate"
                               wire:model.blur="receivedDate" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('receivedDate') border-red-300 @enderror">
                        @error('receivedDate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">When did you receive this document? Leave blank if not applicable.</p>
                    </div>

                    <div>
                        <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date
                            <span class="text-xs text-gray-500 font-normal">(Optional - when you need to submit this)</span>
                        </label>
                        <input type="date" 
                               id="dueDate"
                               wire:model.blur="dueDate" 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                      @error('dueDate') border-red-300 @enderror">
                        @error('dueDate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Is there a deadline for when you need to file or submit this document?</p>
                    </div>
                </div>
            @endif

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes/Description
                    @if(!$isLegalProfessional)
                        <span class="text-xs text-gray-500 font-normal">(Optional - any important details to remember)</span>
                    @endif
                </label>
                <textarea id="description" 
                          wire:model.blur="description" 
                          rows="{{ $isLegalProfessional ? '3' : '4' }}"
                          placeholder="{{ $isLegalProfessional ? 
                              'Additional notes...' : 
                              'e.g., Original signed copy, need to make copies, missing page 3, very important for case' }}"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                 @error('description') border-red-300 @enderror"></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="mt-1 flex justify-between text-xs text-gray-500">
                    <span>{{ mb_strlen($description) }}/500 characters</span>
                    @if(!$isLegalProfessional)
                        <span>Include any special notes about this document</span>
                    @endif
                </div>
            </div>

            {{-- Add Document Button --}}
            <div class="flex justify-end">
                <button wire:click="addDocumentPlaceholder"
                        class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium 
                               text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                               focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled">
                    <div wire:loading.remove>
                        {{ $isLegalProfessional ? 'Add Document' : 'Add This Document' }}
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

    {{-- Guidance Section for Pro-Se Users --}}
    @if(!$isLegalProfessional)
        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Document Tips</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p class="mb-2"><strong>Don't worry if you don't have everything yet!</strong> This is just to help you stay organized.</p>
                        <ul class="space-y-1">
                            <li>• Add documents you already have (contracts, receipts, photos)</li>
                            <li>• Add documents you need to get (medical records, statements)</li>
                            <li>• Add documents you need to file with the court</li>
                            <li>• You can always come back and add more or update details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
