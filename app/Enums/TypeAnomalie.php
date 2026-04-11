<?php

namespace App\Enums;

enum TypeAnomalie: string
{
    case NoteAbsente = 'note_absente';
    case NoteErronee = 'note_erronee';
    case IncoherenceCcSn = 'incoherence_cc_sn';
    case ErreurTranscription = 'erreur_transcription';

    public function label(): string
    {
        return match($this) {
            self::NoteAbsente => 'Note absente',
            self::NoteErronee => 'Note erronée',
            self::IncoherenceCcSn => 'Incohérence CC/SN',
            self::ErreurTranscription => 'Erreur de transcription',
        };
    }
}
