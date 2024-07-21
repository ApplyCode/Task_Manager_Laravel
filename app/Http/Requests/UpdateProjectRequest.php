<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only the user that created the project can update it
        if ($this->user()->id === $this->project->user_id) {
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
            'description' => 'required|min:3|max:500',
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
