@extends('layouts.app')

@section('title', 'My Cases')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    @if(auth()->user()->isLegalProfessional())
                        Case Management
                    @else
                        My Cases
                    @endif
                </h1>
                <p class="text-lg text-gray-600">
                    @if(auth()->user()->isLegalProfessional())
                        Manage all your client cases in one place.
                    @else
                        Track and manage your legal cases.
                    @endif
                </p>
            </div>
            <a href="{{ route('cases.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                @if(auth()->user()->isLegalProfessional())
                    New Case
                @else
                    Create My Case
                @endif
            </a>
        </div>

        <!-- Cases List -->
        @if($cases->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Cases ({{ $cases->total() }})</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($cases as $case)
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <a href="{{ route('cases.show', $case) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $case->name }}
                                            </a>
                                        </h3>
                                        @if($case->case_number)
                                            <span class="ml-2 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                                {{ $case->case_number }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 flex items-center text-sm text-gray-600">
                                        <span class="capitalize">{{ $case->type }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $case->jurisdiction }}</span>
                                        @if($case->venue)
                                            <span class="mx-2">•</span>
                                            <span>{{ $case->venue }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <span>Created {{ $case->created_at->diffForHumans() }}</span>
                                        @if($case->caseParties->count() > 0)
                                            <span class="mx-2">•</span>
                                            <span>{{ $case->caseParties->count() }} {{ Str::plural('party', $case->caseParties->count()) }}</span>
                                        @endif
                                        @if($case->caseDeadlines->count() > 0)
                                            <span class="mx-2">•</span>
                                            <span>{{ $case->caseDeadlines->count() }} {{ Str::plural('deadline', $case->caseDeadlines->count()) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($case->status === 'active') bg-green-100 text-green-800
                                        @elseif($case->status === 'closed') bg-gray-100 text-gray-800
                                        @elseif($case->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($case->status) }}
                                    </span>
                                    <a href="{{ route('cases.show', $case) }}" 
                                       class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($cases->hasPages())
                <div class="mt-6">
                    {{ $cases->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No cases yet</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(auth()->user()->isLegalProfessional())
                        Get started by creating your first client case.
                    @else
                        Get started by creating your case.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('cases.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        @if(auth()->user()->isLegalProfessional())
                            Create First Case
                        @else
                            Create My Case
                        @endif
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 