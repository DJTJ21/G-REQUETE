<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Décision sur votre requête</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f9fb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,36,68,0.08); }
        .header { background: linear-gradient(135deg, #002444, #1a3a5c); padding: 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; letter-spacing: 1px; }
        .header p { color: rgba(255,255,255,0.7); font-size: 13px; margin: 6px 0 0; }
        .verdict-fondee { background: #f0fdf4; border: 2px solid #bbf7d0; margin: 0; padding: 24px 32px; text-align: center; }
        .verdict-nonfondee { background: #fef2f2; border: 2px solid #fecaca; margin: 0; padding: 24px 32px; text-align: center; }
        .verdict-icon { font-size: 36px; margin-bottom: 8px; }
        .verdict-title { font-size: 17px; font-weight: 700; }
        .verdict-fondee .verdict-title { color: #166534; }
        .verdict-nonfondee .verdict-title { color: #991b1b; }
        .body { padding: 32px; }
        .body p { color: #475569; font-size: 14px; line-height: 1.6; }
        .info-box { background: #f8fafc; border-radius: 8px; padding: 16px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .info-row:last-child { border-bottom: none; }
        .info-row span:first-child { color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 11px; }
        .info-row span:last-child { color: #1e293b; font-weight: 600; }
        .note-change { display: flex; align-items: center; justify-content: center; gap: 16px; margin: 16px 0; }
        .note-box { text-align: center; padding: 12px 20px; border-radius: 8px; }
        .note-avant { background: #fee2e2; }
        .note-avant .n { color: #dc2626; font-size: 24px; font-weight: 700; }
        .note-apres { background: #dcfce7; }
        .note-apres .n { color: #16a34a; font-size: 24px; font-weight: 700; }
        .note-label { font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
        .motif-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .motif-box p { color: #7f1d1d; font-size: 13px; margin: 0; }
        .footer { background: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 11px; margin: 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>G-REQUÊTES</h1>
        <p>Système de Gestion des Requêtes Académiques</p>
    </div>

    @if($requete->statut->value === 'traitee_fondee')
    <div class="verdict-fondee">
        <div class="verdict-icon">✅</div>
        <div class="verdict-title">Requête fondée — Correction effectuée</div>
    </div>
    @else
    <div class="verdict-nonfondee">
        <div class="verdict-icon">❌</div>
        <div class="verdict-title">Requête non fondée — Aucune correction</div>
    </div>
    @endif

    <div class="body">
        <p>Bonjour <strong>{{ $requete->etudiant->utilisateur->prenom }}</strong>,</p>
        <p>
            Une décision a été rendue concernant votre requête
            <strong style="font-family: monospace; color: #002444;">{{ $requete->ref_requete }}</strong>.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span>Cours</span>
                <span>{{ $requete->cours->nom_cours }}</span>
            </div>
            <div class="info-row">
                <span>Session</span>
                <span>{{ $requete->session->libelle }}</span>
            </div>
            <div class="info-row">
                <span>Type</span>
                <span>{{ $requete->type_anomalie->label() }}</span>
            </div>
            @if($requete->agent)
            <div class="info-row">
                <span>Traité par</span>
                <span>{{ $requete->agent->utilisateur->nom_complet }}</span>
            </div>
            @endif
            <div class="info-row">
                <span>Date de décision</span>
                <span>{{ $requete->date_traitement?->format('d/m/Y à H:i') }}</span>
            </div>
        </div>

        @if($requete->statut->value === 'traitee_fondee' && $requete->note)
        <p style="font-weight: 600; color: #166534; margin-bottom: 8px;">Votre note a été corrigée :</p>
        <div class="note-change">
            <div class="note-box note-avant">
                <div class="note-label">Avant</div>
                <div class="n">{{ $requete->note->note_avant ?? '—' }}</div>
                <div style="font-size: 11px; color: #94a3b8;">/20</div>
            </div>
            <span style="font-size: 20px; color: #94a3b8;">→</span>
            <div class="note-box note-apres">
                <div class="note-label">Après</div>
                <div class="n">{{ $requete->note->note_apres }}</div>
                <div style="font-size: 11px; color: #94a3b8;">/20</div>
            </div>
        </div>
        @endif

        @if($requete->motif_rejet)
        <div class="motif-box">
            <p><strong>Motif :</strong> {{ $requete->motif_rejet }}</p>
        </div>
        @endif

        <p style="margin-top: 16px;">Pour plus de détails, connectez-vous à votre espace étudiant G-REQUÊTES.</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} G-REQUÊTES — Service de Scolarité<br>Cet email est automatique, merci de ne pas y répondre.</p>
    </div>
</div>
</body>
</html>
