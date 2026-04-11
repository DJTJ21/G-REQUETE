<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Requête reçue</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f9fb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,36,68,0.08); }
        .header { background: linear-gradient(135deg, #002444, #1a3a5c); padding: 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; letter-spacing: 1px; }
        .header p { color: rgba(255,255,255,0.7); font-size: 13px; margin: 6px 0 0; }
        .body { padding: 32px; }
        .body h2 { color: #002444; font-size: 16px; margin-bottom: 6px; }
        .body p { color: #475569; font-size: 14px; line-height: 1.6; }
        .info-box { background: #f8fafc; border-radius: 8px; padding: 16px; margin: 20px 0; border-left: 4px solid #002444; }
        .info-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .info-row span:first-child { color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 11px; }
        .info-row span:last-child { color: #1e293b; font-weight: 600; }
        .badge { display: inline-block; background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
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
    <div class="body">
        <h2>Bonjour {{ $requete->etudiant->utilisateur->prenom }},</h2>
        <p>Votre requête académique a bien été enregistrée et est en cours de traitement par le service de scolarité.</p>

        <div class="info-box">
            <div class="info-row">
                <span>Référence</span>
                <span style="font-family: monospace; color: #002444;">{{ $requete->ref_requete }}</span>
            </div>
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
            <div class="info-row">
                <span>Statut</span>
                <span><span class="badge">En attente de traitement</span></span>
            </div>
            <div class="info-row">
                <span>Soumise le</span>
                <span>{{ $requete->date_soumission->format('d/m/Y à H:i') }}</span>
            </div>
        </div>

        <p>Vous serez notifié(e) par email dès qu'une décision sera rendue. Vous pouvez également suivre l'évolution de votre requête en vous connectant à votre espace étudiant.</p>
        <p style="margin-top: 16px; color: #64748b; font-size: 12px;">⚠️ Gardez précieusement la référence <strong>{{ $requete->ref_requete }}</strong> pour tout suivi.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} G-REQUÊTES — Service de Scolarité<br>Cet email est automatique, merci de ne pas y répondre.</p>
    </div>
</div>
</body>
</html>
