<?php

namespace App\Http\Requests;

use App\Enums\TypeAnomalie;
use App\Models\SessionExamen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SoumettreRequeteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cours_id'         => ['required', 'exists:cours,id'],
            'session_id'       => ['required', 'exists:sessions_examens,id'],
            'type_anomalie'    => ['required', new Enum(TypeAnomalie::class)],
            'description'      => ['nullable', 'string', 'max:500'],
            'pieces_jointes'   => ['required', 'array', 'min:1'],
            'pieces_jointes.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $session = SessionExamen::find($this->session_id);
            if ($session && ! $session->fenetreOuverte()) {
                $validator->errors()->add('session_id', 'Le délai de soumission de 72h après la publication des résultats est dépassé.');
            }

            if ($session && ! $session->est_active) {
                $validator->errors()->add('session_id', 'Cette session n\'est pas active.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'cours_id.required'       => 'Veuillez sélectionner un cours.',
            'cours_id.exists'         => 'Le cours sélectionné est invalide.',
            'session_id.required'     => 'Veuillez sélectionner une session.',
            'type_anomalie.required'  => 'Veuillez sélectionner le type d\'anomalie.',
            'pieces_jointes.required' => 'Au moins une pièce justificative est requise.',
            'pieces_jointes.*.mimes'  => 'Les fichiers doivent être de type PDF, JPG ou PNG.',
            'pieces_jointes.*.max'    => 'Chaque fichier ne doit pas dépasser 10 Mo.',
        ];
    }
}
