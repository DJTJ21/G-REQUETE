<?php

namespace App\Services;

use App\Models\PieceJointe;
use App\Models\Requete;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FichierService
{
    public function sauvegarder(UploadedFile $fichier, Requete $requete): PieceJointe
    {
        $nomPropre  = Str::slug(pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME));
        $extension  = $fichier->getClientOriginalExtension();
        $nomUnique  = $nomPropre . '_' . uniqid() . '.' . $extension;
        $dossier    = 'pieces_jointes/' . $requete->ref_requete;

        $chemin = $fichier->storeAs($dossier, $nomUnique, 'local');

        return PieceJointe::create([
            'requete_id'     => $requete->id,
            'nom_fichier'    => $fichier->getClientOriginalName(),
            'chemin_fichier' => $chemin,
            'type_mime'      => $fichier->getMimeType(),
            'taille'         => $fichier->getSize(),
        ]);
    }

    public function supprimer(PieceJointe $pj): void
    {
        Storage::disk('local')->delete($pj->chemin_fichier);
        $pj->delete();
    }
}
