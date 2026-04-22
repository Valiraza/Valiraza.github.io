<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation acceptee</title>
</head>
<body style="margin:0;padding:0;background:#f6f8fb;font-family:Arial,sans-serif;color:#1f2937;">
    <div style="max-width:640px;margin:32px auto;padding:0 16px;">
        <div style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,0.08);">
            <div style="background:#4a7c2c;padding:24px 28px;color:#ffffff;">
                <h1 style="margin:0;font-size:24px;">Reservation acceptee</h1>
                <p style="margin:8px 0 0;font-size:14px;opacity:0.92;">Votre demande a bien ete validee par notre equipe.</p>
            </div>

            <div style="padding:28px;">
                <p style="margin-top:0;">Bonjour {{ $reservation->nom }},</p>

                <p>Nous avons le plaisir de vous confirmer que votre reservation a ete acceptee.</p>

                <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:12px;padding:18px 20px;margin:20px 0;">
                    <p style="margin:0 0 10px;"><strong>Circuit :</strong> {{ $reservation->circuit_nom ?: ($reservation->circuit->nom ?? 'Non renseigne') }}</p>
                    <p style="margin:0 0 10px;"><strong>Voyageurs :</strong> {{ $reservation->voyageurs }}</p>
                    <p style="margin:0 0 10px;"><strong>Places :</strong> {{ $reservation->places ?: 'Non specifiees' }}</p>
                    <p style="margin:0;"><strong>Date de reservation :</strong> {{ optional($reservation->created_at)->format('d/m/Y H:i') }}</p>
                </div>

                <p>Nous vous remercions pour votre confiance et restons a votre disposition pour toute question complementaire.</p>

                <p style="margin-bottom:0;">L'equipe Autochtone</p>
            </div>
        </div>
    </div>
</body>
</html>
