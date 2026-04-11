<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>PV {{ $pv->ref_pv }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
        }
        .page { padding: 28px 36px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; border-bottom: 2px solid #002444; margin-bottom: 18px; }
        .header-left h1 { font-size: 18px; font-weight: 700; color: #002444; letter-spacing: 0.5px; }
        .header-left p { font-size: 9px; color: #64748b; margin-top: 2px; }
        .header-right { text-align: right; }
        .header-right .ref { font-size: 11px; font-weight: 700; color: #002444; font-family: monospace; }
        .header-right .date { font-size: 9px; color: #64748b; margin-top: 2px; }

        /* Meta section */
        .meta { display: flex; gap: 24px; background: #f8fafc; border-radius: 6px; padding: 12px 16px; margin-bottom: 18px; border: 1px solid #e2e8f0; }
        .meta-item label { font-size: 8px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 3px; }
        .meta-item span { font-size: 10px; font-weight: 600; color: #1e293b; }

        /* Title */
        .section-title { font-size: 11px; font-weight: 700; color: #002444; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        thead tr { background: #002444; }
        thead th { padding: 7px 8px; text-align: left; color: #ffffff; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 6px 8px; font-size: 9px; color: #334155; vertical-align: middle; }
        .badge-fondee { background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 8px; font-size: 8px; font-weight: 700; }
        .badge-nonfondee { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 8px; font-size: 8px; font-weight: 700; }
        .note-apres { font-weight: 700; color: #16a34a; }
        .ref-cell { font-family: monospace; font-weight: 700; color: #002444; }

        /* Summary boxes */
        .summary { display: flex; gap: 12px; margin-bottom: 18px; }
        .summary-box { flex: 1; text-align: center; padding: 10px; border-radius: 6px; }
        .summary-box.total { background: #eff6ff; border: 1px solid #bfdbfe; }
        .summary-box.fondees { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .summary-box.nonfondees { background: #fef2f2; border: 1px solid #fecaca; }
        .summary-box .num { font-size: 20px; font-weight: 700; }
        .summary-box .lbl { font-size: 8px; color: #64748b; text-transform: uppercase; margin-top: 2px; }
        .summary-box.total .num { color: #1d4ed8; }
        .summary-box.fondees .num { color: #16a34a; }
        .summary-box.nonfondees .num { color: #dc2626; }

        /* Signature */
        .signature-section { display: flex; justify-content: space-between; margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
        .signature-box { width: 45%; }
        .signature-box .sig-label { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 48px; }
        .signature-box .sig-line { border-top: 1px solid #94a3b8; padding-top: 4px; }
        .signature-box .sig-name { font-size: 9px; font-weight: 700; color: #1e293b; }
        .signature-box .sig-role { font-size: 8px; color: #64748b; }

        /* Footer */
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; }
        .footer span { font-size: 8px; color: #94a3b8; }

        /* Watermark */
        .watermark { position: fixed; bottom: 60px; right: 36px; font-size: 60px; font-weight: 900; color: rgba(0,36,68,0.04); transform: rotate(-30deg); pointer-events: none; }
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">G-REQUÊTES</div>

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <h1>PROCÈS-VERBAL DE DÉLIBÉRATION</h1>
            <p>Système de Gestion des Requêtes Académiques — G-REQUÊTES</p>
        </div>
        <div class="header-right">
            <div class="ref">{{ $pv->ref_pv }}</div>
            <div class="date">Généré le {{ $pv->date_generation->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    {{-- Meta --}}
    <div class="meta">
        <div class="meta-item">
            <label>Session</label>
            <span>{{ $pv->session->libelle }}</span>
        </div>
        <div class="meta-item">
            <label>Année académique</label>
            <span>{{ $pv->session->annee_acad }}</span>
        </div>
        <div class="meta-item">
            <label>Agent responsable</label>
            <span>{{ $pv->agent->utilisateur->nom_complet }}</span>
        </div>
        <div class="meta-item">
            <label>Service</label>
            <span>{{ $pv->agent->service }}</span>
        </div>
        <div class="meta-item">
            <label>Nombre de requêtes</label>
            <span>{{ $pv->lignes->count() }}</span>
        </div>
    </div>

    {{-- Summary --}}
    @php
        $total = $pv->lignes->count();
        $fondees = $pv->lignes->filter(fn($l) => $l->requete->statut->value === 'traitee_fondee')->count();
        $nonFondees = $total - $fondees;
    @endphp
    <div class="summary">
        <div class="summary-box total">
            <div class="num">{{ $total }}</div>
            <div class="lbl">Total requêtes</div>
        </div>
        <div class="summary-box fondees">
            <div class="num">{{ $fondees }}</div>
            <div class="lbl">Fondées</div>
        </div>
        <div class="summary-box nonfondees">
            <div class="num">{{ $nonFondees }}</div>
            <div class="lbl">Non fondées</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="section-title">Détail des requêtes traitées</div>
    <table>
        <thead>
            <tr>
                <th style="width:25px">#</th>
                <th style="width:80px">Référence</th>
                <th>Étudiant</th>
                <th style="width:60px">Matricule</th>
                <th>Cours</th>
                <th>Anomalie</th>
                <th style="width:50px">Statut</th>
                <th style="width:45px">Note avant</th>
                <th style="width:45px">Note après</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pv->lignes->sortBy('ordre_affichage') as $ligne)
            @php $req = $ligne->requete; @endphp
            <tr>
                <td>{{ $ligne->ordre_affichage + 1 }}</td>
                <td class="ref-cell">{{ $req->ref_requete }}</td>
                <td>{{ $req->etudiant->utilisateur->nom_complet }}</td>
                <td>{{ $req->etudiant->matricule }}</td>
                <td>{{ Str::limit($req->cours->nom_cours, 30) }}</td>
                <td>{{ $req->type_anomalie->label() }}</td>
                <td>
                    @if($req->statut->value === 'traitee_fondee')
                        <span class="badge-fondee">Fondée</span>
                    @else
                        <span class="badge-nonfondee">Non fondée</span>
                    @endif
                </td>
                <td style="text-align:center">{{ $req->note?->note_avant ?? '—' }}</td>
                <td style="text-align:center" class="note-apres">{{ $req->note?->note_apres ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Signature --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="sig-label">L'Agent de Scolarité</div>
            <div class="sig-line">
                <div class="sig-name">{{ $pv->agent->utilisateur->nom_complet }}</div>
                <div class="sig-role">{{ $pv->agent->service }}</div>
            </div>
        </div>
        <div class="signature-box" style="text-align:right">
            <div class="sig-label" style="text-align:right">Le Directeur des Études</div>
            <div class="sig-line">
                <div class="sig-name">&nbsp;</div>
                <div class="sig-role">Cachet et signature</div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>Document généré automatiquement par G-REQUÊTES — {{ config('app.name') }}</span>
        <span>{{ $pv->ref_pv }} — {{ $pv->date_generation->format('d/m/Y à H:i:s') }}</span>
    </div>
</div>
</body>
</html>
