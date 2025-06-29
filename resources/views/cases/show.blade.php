@extends('layouts.app')

@section('title', $case->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('cases.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Cases</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($case->name, 30) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $case->name }}</h1>
                        @if($case->case_number)
                            <span class="ml-3 px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-lg">
                                {{ $case->case_number }}
                            </span>
                        @endif
                        <span class="ml-3 px-3 py-1 text-sm font-medium rounded-lg
                            @if($case->status === 'active') bg-green-100 text-green-800
                            @elseif($case->status === 'closed') bg-gray-100 text-gray-800
                            @elseif($case->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($case->status) }}
                        </span>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-600">
                        <span class="capitalize">{{ $case->type }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $case->jurisdiction }}</span>
                        @if($case->venue)
                            <span class="mx-2">•</span>
                            <span>{{ $case->venue }}</span>
                        @endif
                        <span class="mx-2">•</span>
                        <span>Created {{ $case->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('cases.edit', $case) }}" 
                       class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Case
                    </a>
                    <button type="button" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors inline-flex items-center opacity-50 cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generate Document
                        <span class="text-xs ml-1">(Coming Soon)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Case Description -->
        @if($case->description)
            <div class="mb-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">Case Description</h2>
                <p class="text-gray-700 leading-relaxed">{{ $case->description }}</p>
            </div>
        @endif

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Case Details</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Parties Count -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $case->caseParties->count() }}</div>
                        <div class="text-sm text-gray-600">{{ Str::plural('Party', $case->caseParties->count()) }}</div>
                    </div>
                    
                    <!-- Documents Count -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $case->caseDocuments->count() }}</div>
                        <div class="text-sm text-gray-600">{{ Str::plural('Document', $case->caseDocuments->count()) }}</div>
                    </div>
                    
                    <!-- Deadlines Count -->
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $case->caseDeadlines->count() }}</div>
                        <div class="text-sm text-gray-600">{{ Str::plural('Deadline', $case->caseDeadlines->count()) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coming Soon Notice -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">More Features Coming Soon</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>The detailed case view with party management, document handling, deadline tracking, and AI-powered document generation will be implemented in upcoming tasks.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 