<?php

namespace App\Http\Controllers;

use App\Models\LegalCase;
use App\Livewire\CaseCreationWizard;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CaseController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the case creation wizard.
     * 
     * This method serves as the entry point for the Case Creation Wizard.
     * The actual wizard implementation is handled by the CaseCreationWizard Livewire component.
     */
    public function create(): View
    {
        return view('cases.create');
    }

    /**
     * Handle the completion of the case creation wizard.
     * 
     * This method processes the final submission from the CaseCreationWizard component
     * and creates the case with all its related data. Validation is handled by the
     * StoreCaseRequest which validates data from the wizard session.
     */
    public function store(StoreCaseRequest $request): RedirectResponse
    {
        try {
            return \DB::transaction(function () use ($request) {
                // Get and validate case data from session using the Form Request
                $caseData = $request->getValidatedCaseData();

            // Create the case
            $case = LegalCase::create([
                'user_id' => Auth::id(),
                'name' => $caseData['name'],
                'case_number' => $caseData['case_number'],
                'type' => $caseData['type'],
                'jurisdiction' => $caseData['jurisdiction'],
                'venue' => $caseData['venue'],
                'description' => $caseData['description'],
                'status' => 'active',
            ]);

            // Handle parties if provided
            if (isset($caseData['parties']) && is_array($caseData['parties'])) {
                foreach ($caseData['parties'] as $partyData) {
                    $case->caseParties()->create([
                        'name' => $partyData['name'],
                        'role' => $partyData['role'] ?? $partyData['type'], // Handle both 'role' and 'type' keys
                        'party_type' => $partyData['party_type'] ?? $partyData['category'] ?? 'individual',
                        'contact_info' => [
                            'email' => $partyData['email'] ?? null,
                            'phone' => $partyData['phone'] ?? null,
                        ],
                        'address' => $partyData['address'] ?? null,
                        'email' => $partyData['email'] ?? null,
                        'phone' => $partyData['phone'] ?? null,
                    ]);
                }
            }

            // Handle key dates if provided
            if (isset($caseData['dates']) && is_array($caseData['dates'])) {
                foreach ($caseData['dates'] as $dateData) {
                    $case->caseDeadlines()->create([
                        'title' => $dateData['title'],
                        'description' => $dateData['description'] ?? null,
                        'due_date' => $dateData['date'] ?? $dateData['due_date'],
                        'reminder_date' => $dateData['reminder_date'] ?? null,
                        'priority' => $dateData['priority'] ?? 'medium',
                        'status' => 'pending',
                    ]);
                }
            }
            // Handle documents if provided (placeholder for future file upload)
            if (isset($caseData['documents']) && is_array($caseData['documents'])) {
                // TODO: Implement file upload functionality
                Log::info('Documents metadata received but file upload not yet implemented', [
                    'case_id' => $case->id,
                    'document_count' => count($caseData['documents'])
                ]);
            }

            // Clear the session data now that the case is created
            session()->forget('case_creation_data');
            
            // Clear Livewire wizard session data as well
            CaseCreationWizard::clearWizardSessionStatic();

            Log::info('Case created successfully', [
                'case_id' => $case->id,
                'user_id' => Auth::id(),
                'user_type' => Auth::user()->user_type,
            ]);

            return redirect()
                ->route('cases.show', $case)
                ->with('success', 'Your case has been created successfully!');
            });
        } catch (ValidationException $e) {
            Log::warning('Case creation validation failed', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('cases.create')
                ->withErrors($e->errors())
                ->with('error', 'Please check your case information and try again.');
        } catch (\Exception $e) {
            Log::error('Failed to create case', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'session_data' => session('case_creation_data'),
            ]);

            return redirect()
                ->route('cases.create')
                ->with('error', 'There was an error creating your case. Please try again.');
        }
    }

    public function show(LegalCase $case): View
    {
        // Ensure the user can only view their own cases
        if ($case->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to view this case.');
        }

        $case->load([
            'caseParties',
            'caseDocuments',
            'caseDeadlines' => function ($query) {
                $query->orderBy('due_date', 'asc');
            }
        ]);

        return view('cases.show', compact('case'));
    }

    /**
     * Display a listing of the user's cases.
     */
    public function index(): View
    {
        // Eager load relationships to prevent N+1 queries when accessing
        // $case->caseParties->count() and $case->caseDeadlines->count() in the view
        $cases = LegalCase::where('user_id', Auth::id())
            ->with(['caseParties', 'caseDeadlines'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cases.index', compact('cases'));
    }

    /**
     * Show the form for editing the specified case.
     */
    public function edit(LegalCase $case): View
    {
        // Ensure the user can only edit their own cases
        if ($case->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to edit this case.');
        }

        $case->load(['caseParties', 'caseDocuments', 'caseDeadlines']);

        return view('cases.edit', compact('case'));
    }

    /**
     * Update the specified case.
     */
    public function update(UpdateCaseRequest $request, LegalCase $case): RedirectResponse
    {
        // Authorization is handled by the UpdateCaseRequest
        $case->update($request->validated());

        return redirect()
            ->route('cases.show', $case)
            ->with('success', 'Case updated successfully!');
    }

    /**
     * Remove the specified case from storage.
     */
    public function destroy(LegalCase $case): RedirectResponse
    {
        // Ensure the user can only delete their own cases
        if ($case->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this case.');
        }

        try {
            $case->delete(); // This will use soft deletes

            Log::info('Case deleted successfully', [
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('cases.index')
                ->with('success', 'Case deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to delete case', [
                'error' => $e->getMessage(),
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->back()
                ->with('error', 'There was an error deleting the case. Please try again.');
        }
    }
} 