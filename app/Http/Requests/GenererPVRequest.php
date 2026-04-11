<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenererPVRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'exists:sessions_examens,id'],
            'filiere_id' => ['required', 'exists:filieres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => 'Veuillez sélectionner une session.',
            'filiere_id.required' => 'Veuillez sélectionner une filière.',
        ];
    }
}
