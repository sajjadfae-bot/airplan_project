<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AirplaneStoreValidation extends FormRequest
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
            'name'             => ['required', 'min:3', 'max:50'],
            'model'            => ['required', 'min:2', 'max:50'],
            'registration_code' => ['required', Rule::unique('airplanes', 'registration_code')->ignore($this->route('airplane'))],
            'capacity'         => ['required', 'integer', 'min:1'],
            'status'           => ['required', 'in:active,inactive'],
        ];
    }
}
