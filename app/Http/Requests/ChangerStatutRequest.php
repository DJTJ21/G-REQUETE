<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangerStatutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision'      => ['required', 'in:en_cours_verification,traitee_fondee,traitee_non_fondee'],
            'motif_rejet'   => ['required_if:decision,traitee_non_fondee', 'nullable', 'string', 'max:1000'],
            'nouvelle_note' => ['required_if:decision,traitee_fondee', 'nullable', 'numeric', 'min:0', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.required'         => 'Une décision est requise.',
            'decision.in'               => 'La décision sélectionnée est invalide.',
            'motif_rejet.required_if'   => 'Un motif de rejet est obligatoire pour une décision non fondée.',
            'nouvelle_note.required_if' => 'La nouvelle note est obligatoire pour une décision fondée.',
            'nouvelle_note.min'         => 'La note ne peut pas être négative.',
            'nouvelle_note.max'         => 'La note ne peut pas dépasser 20.',
        ];
    }
}
