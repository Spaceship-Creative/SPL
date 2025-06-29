<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateCaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated and owns the case
        return Auth::check() && $this->route('case')->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $caseId = $this->route('case')->id;

        return [
            'name' => 'required|string|max:255',
            'case_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('cases', 'case_number')->ignore($caseId)
            ],
            'type' => 'required|string|max:100',
            'jurisdiction' => 'required|string|max:255',
            'venue' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            // Status is required for updates because:
            // 1. Users should explicitly choose a status when editing
            // 2. This allows changing status (active → closed, pending, etc.)
            // Note: This differs from StoreCaseRequest where status is nullable
            'status' => 'required|in:active,closed,pending,archived',
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
            'status.required' => 'The case status is required.',
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
}
