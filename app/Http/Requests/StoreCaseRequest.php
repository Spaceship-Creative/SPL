<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StoreCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can create cases
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'case_number' => 'nullable|string|max:100|unique:cases,case_number',
            'type' => 'required|string|max:100',
            'jurisdiction' => 'required|string|max:255',
            'venue' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            // Status is nullable for creation because:
            // 1. The wizard doesn't collect status from users
            // 2. The controller hardcodes status to 'active' 
            // 3. The database has a default of 'active'
            // Note: This differs from UpdateCaseRequest where status is required
            'status' => 'nullable|string|in:active,closed,pending,archived',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The case name is required.',
            'name.max' => 'The case name may not be greater than 255 characters.',
            'case_number.unique' => 'This case number is already in use.',
            'type.required' => 'The case type is required.',
            'jurisdiction.required' => 'The jurisdiction is required.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'status.in' => 'The case status must be one of: active, closed, pending, archived.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'case_number' => 'case number',
        ];
    }

    /**
     * Validate case data from session (used by the wizard workflow).
     * 
     * @param array $caseData The case data to validate
     * @throws ValidationException
     */
    public function validateCaseData(array $caseData): void
    {
        $validator = Validator::make($caseData, $this->rules(), $this->messages(), $this->attributes());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Get the validated case data from the session.
     * 
     * @return array
     * @throws ValidationException
     */
    public function getValidatedCaseData(): array
    {
        $sessionData = session('case_creation_data');
        
        if (!$sessionData || !isset($sessionData['case_data'])) {
            throw ValidationException::withMessages([
                'session' => 'Session expired. Please try creating your case again.'
            ]);
        }

        $caseData = $sessionData['case_data'];
        $this->validateCaseData($caseData);
        
        return $caseData;
    }
}
