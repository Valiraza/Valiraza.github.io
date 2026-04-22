<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .message {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #3498db;
            margin: 20px 0;
        }
        .reply {
            background-color: #e8f4fd;
            padding: 15px;
            border-left: 4px solid #2ecc71;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Autochtone Tour</h1>
        <p>Réponse à votre message</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $contact->name }},</p>

        <p>Nous avons bien reçu votre message et nous vous répondons ci-dessous :</p>

        <div class="message">
            <h3>Votre message original :</h3>
            <p><strong>Email :</strong> {{ $contact->email }}</p>
            <p>{{ $contact->message }}</p>
        </div>

        <div class="reply">
            <h3>Notre réponse :</h3>
            <p>{{ $reply }}</p>
        </div>

        <p>Si vous avez d'autres questions, n'hésitez pas à nous contacter.</p>

        <p>Cordialement,<br>
        L'équipe Autochtone Tour</p>
    </div>

    <div class="footer">
        <p>Cette réponse a été envoyée automatiquement depuis notre système de gestion.</p>
        <p>&copy; 2024 Autochtone Tour. Tous droits réservés.</p>
    </div>
</body>
</html>