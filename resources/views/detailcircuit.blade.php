<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Détail Circuit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    @vite([
        'resources/detailcircuit.css',
        'resources/js/loaders/detail-circuit-loader.js',
    ])
</head>
<body>
    <div id="detail-circuit-app"></div>

    @isset($circuit)
        <script id="detail-circuit-data" type="application/json">@json($circuit)</script>
    @endisset
</body>
</html>
