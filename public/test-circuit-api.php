<?php
/**
 * Test de l'API Circuit avec slug
 * 
 * Accédez à cette page depuis votre navigateur:
 * http://localhost:8000/test-circuit-api.php
 */

// Simuler un appel API pour vérifier le slug
$slug = $_GET['slug'] ?? 'incontournables-madagascar';

// Si c'est une requête AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['api'])) {
    header('Content-Type: application/json');
    
    // Charger Laravel pour accéder à la base de données
    require __DIR__ . '/vendor/autoload.php';
    require __DIR__ . '/bootstrap/app.php';
    
    try {
        $app = require_once __DIR__ . '/bootstrap/app.php';
        
        // Vous pouvez aussi utiliser curl pour tester l'endpoint
        // c'est plus sûr de le faire via HTTP
        exit(json_encode([
            'message' => 'Utilisez curl ou le navigateur pour tester',
            'endpoint' => '/api/circuits/by-slug/' . $slug,
            'slug' => $slug
        ]));
    } catch (Exception $e) {
        exit(json_encode(['error' => $e->getMessage()]));
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Circuit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .test-section { margin-top: 30px; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test de l'API Circuit par Slug</h1>
        
        <div class="test-section">
            <h3>Instructions de test</h3>
            <ol>
                <li>Créez ou modifiez un circuit dans l'admin (Circuit.vue)</li>
                <li>Notez le slug généré (ex: "incontournables-madagascar")</li>
                <li>Visitez l'URL du circuit: <code>/circuit/{slug}</code></li>
                <li>Ouvrez la console (F12) et cherchez les erreurs</li>
            </ol>
        </div>

        <div class="test-section">
            <h3>Tester l'API directement</h3>
            <p>
                <label>Slug du circuit:</label>
                <input type="text" id="slugInput" class="form-control" value="<?php echo htmlspecialchars($slug); ?>" />
            </p>
            <button class="btn btn-primary" onclick="testAPI()">Tester l'API</button>
            <div id="result" style="margin-top: 20px;"></div>
        </div>

        <div class="test-section">
            <h3>Points de vérification</h3>
            <ul>
                <li><strong>Pas d'erreur 500:</strong> La réponse HTTP doit être 200</li>
                <li><strong>Structure correcte:</strong> Les données du circuit s'affichent avec tous les champs</li>
                <li><strong>Jours du programme:</strong> Seuls les jours avec un numéro spécifié s'affichent</li>
                <li><strong>Images:</strong> Les chemins d'images sont correctement résolus (/storage/...)</li>
                <li><strong>Détails:</strong> Tous les détails (prix, specs, points forts) s'affichent dynamiquement</li>
            </ul>
        </div>

        <div class="test-section">
            <h3>Troubleshooting</h3>
            <table class="table">
                <tr>
                    <td><strong>Erreur 500</strong></td>
                    <td>Vérifiez que le slug existe dans la base de données. Vérifiez aussi les logs Laravel.</td>
                </tr>
                <tr>
                    <td><strong>Page vide</strong></td>
                    <td>Vérifiez que le circuit est publié (statut: "Publie") si vous filtrez par statut.</td>
                </tr>
                <tr>
                    <td><strong>Jours vides s'affichent</strong></td>
                    <td>Le script filtre maintenant les jours sans numéro spécifié. Vérifiez la console F12.</td>
                </tr>
                <tr>
                    <td><strong>Images ne chargent pas</strong></td>
                    <td>Vérifiez que les fichiers existent dans storage/app/public/circuits/</td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        async function testAPI() {
            const slug = document.getElementById('slugInput').value;
            const resultDiv = document.getElementById('result');
            
            if (!slug) {
                resultDiv.innerHTML = '<div class="alert alert-danger">Veuillez entrer un slug</div>';
                return;
            }

            resultDiv.innerHTML = '<div class="alert alert-info">Chargement...</div>';

            try {
                const response = await fetch(`/api/circuits/by-slug/${slug}`);
                
                if (!response.ok) {
                    throw new Error(`Erreur ${response.status}: ${response.statusText}`);
                }

                const circuit = await response.json();
                
                let html = '<div class="alert alert-success">✓ Circuit trouvé!</div>';
                html += '<h5>Données du circuit</h5>';
                html += '<pre>' + JSON.stringify(circuit, null, 2) + '</pre>';
                html += '<h5>Analyse rapide</h5>';
                html += '<ul>';
                html += `<li>Nom: ${circuit.nom}</li>`;
                html += `<li>Slug: ${circuit.slug}</li>`;
                html += `<li>Destination: ${circuit.destination}</li>`;
                html += `<li>Nombre de jours: ${(circuit.programme || []).length}</li>`;
                
                if (circuit.programme && circuit.programme.length > 0) {
                    html += '<li>Jours du programme:';
                    html += '<ul>';
                    circuit.programme.forEach((day, i) => {
                        html += `<li>Jour ${day.jour}: ${day.titre || '(sans titre)'}</li>`;
                    });
                    html += '</ul></li>';
                }
                
                html += `<li>État: ${circuit.statut}</li>`;
                html += '</ul>';
                
                resultDiv.innerHTML = html;
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-danger">✗ Erreur: ${error.message}</div>`;
            }
        }

        // Tester automatiquement à la première charge
        window.addEventListener('load', () => {
            if (document.getElementById('slugInput').value) {
                testAPI();
            }
        });
    </script>
</body>
</html>
