<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only the user that created the patient can update it
        if ($this->user()->id === $this->patient->user_id) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'address' => 'required|min:3|max:500',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
            'squad_id' => auth()->user()->squad_id,
        ]);
    }
}
