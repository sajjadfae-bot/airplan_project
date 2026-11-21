<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PilotStoreValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'          => ['required', 'min:3', 'max:50'],
            'license_number' => ['required', Rule::unique('pilots', 'license_number')->ignore($this->route('pilot'))],
            'phone'         => ['nullable', 'string', 'max:20'],
            'password'      => [$this->isMethod('POST') ? 'required' : 'sometimes', 'string', 'min:6'],
            'status'        => ['required', 'in:active,inactive'],
        ];
    }
}
