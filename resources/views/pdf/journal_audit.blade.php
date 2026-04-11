<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }
    h1 { font-size: 14px; color: #002444; margin: 0 0 4px; }
    .sub { font-size: 8px; color: #64748b; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #002444; color: #fff; padding: 5px 8px; text-align: left; font-size: 8px; }
    td { padding: 4px 8px; border-bottom: 1px solid #e2e8f0; }
    tr:nth-child(even) td { background: #f8fafc; }
    .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; }
    .green { background: #dcfce7; color: #166534; }
    .red   { background: #fee2e2; color: #991b1b; }
    .blue  { background: #dbeafe; color: #1e40af; }
    .amber { background: #fef3c7; color: #92400e; }
    .gray  { background: #f1f5f9; color: #475569; }
</style>
</head>
<body>
<h1>Journal d'Audit — G-REQUÊTES</h1>
<p class="sub">Généré le {{ $generatedAt->format('d/m/Y à H:i') }} · {{ $historiques->count() }} entrées (max 200)</p>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type d'action</th>
            <th>Acteur</th>
            <th>Requête</th>
            <th>Détails</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
        @foreach($historiques as $h)
        @php
            $acteur = $h->agent?->utilisateur ?? $h->admin?->utilisateur;
            $cls = match(true) {
                str_contains($h->type_action, 'decision') => 'blue',
                str_contains($h->type_action, 'creation') => 'green',
                str_contains($h->type_action, 'toggle')   => 'amber',
                str_contains($h->type_action, 'pv')       => 'gray',
                default => 'gray',
            };
        @endphp
        <tr>
            <td style="white-space:nowrap">{{ $h->created_at->format('d/m/Y H:i') }}</td>
            <td><span class="badge {{ $cls }}">{{ str_replace('_', ' ', $h->type_action) }}</span></td>
            <td>{{ $acteur?->nom_complet ?? 'Système' }}</td>
            <td style="font-family:monospace">{{ $h->requete?->ref_requete ?? '—' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($h->details, 80) }}</td>
            <td style="font-family:monospace;color:#94a3b8">{{ $h->ip_address }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
