<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VillagerRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today|after:' . now()->subYears(150)->format('Y-m-d'),
            'gender' => 'required|in:male,female,other',
            'household_id' => 'required|string|max:100',
            'village' => 'nullable|string|max:100',
            'ward' => 'nullable|string|max:100',
            'zone' => 'nullable|string|max:100',
            'passport_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nin' => ['nullable', 'string', 'regex:/^[0-9]{11}$/'],
            'phone_number' => ['nullable', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'email' => 'nullable|email|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:100',
            'marital_status' => 'nullable|in:single,married,widowed,divorced',
            'occupation' => 'nullable|string|max:255',
            'education_level' => 'nullable|in:none,primary,secondary,tertiary',
            'health_status' => 'nullable|string|max:500',
            'fingerprint_data' => 'nullable|string',
            'facial_photo' => 'nullable|string',
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
