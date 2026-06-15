<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VillagerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'sometimes|string|max:255',
            'date_of_birth' => 'sometimes|date|before_or_equal:today|after:' . now()->subYears(150)->format('Y-m-d'),
            'gender' => 'sometimes|in:male,female',
            'household_id' => 'sometimes|string|max:100',
            'phone_number' => ['sometimes', 'nullable', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'bank_account_number' => 'sometimes|nullable|string|max:50',
            'nin' => ['sometimes', 'nullable', 'string', 'regex:/^[0-9]{11}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'Date of birth cannot be in the future.',
            'date_of_birth.after' => 'Date of birth is invalid (more than 150 years in the past).',
            'phone_number.regex' => 'Phone number must be 10-15 digits with optional country code prefix.',
            'nin.regex' => 'NIN must be exactly 11 numeric digits.',
        ];
    }
}
