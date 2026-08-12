<?php
require_once "conexao.php";

// Filtros de busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Consulta SQL adaptada à sua estrutura atual de banco de dados
$sql = "SELECT p.*, f.url_imagem 
        FROM pontoturistico p
        LEFT JOIN fotos f ON p.id_fotos = f.id_fotos
        WHERE p.nome LIKE :busca OR p.descricao LIKE :busca OR p.endereco LIKE :busca";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':busca', "%$busca%");
$stmt->execute();
$pontos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AltoTuristc - Descubra o Alto Vale</title>
    <link rel="stylesheet" href="estilo.css">
    
    <!-- Leaflet CSS (Mapa Interativo) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <h1>Alto<span>Turistc</span></h1>
        </div>
        <nav>
            <a href="index.php">Início</a>
            <a href="#mapa">Mapa</a>
            <a href="#locais">Pontos Turísticos</a>
        </nav>
    </header>

    <section class="hero">
        <h2>Explore as maravilhas do Alto Vale do Itajaí</h2>
        <form method="GET" action="index.php" class="search-box">
            <input type="text" name="busca" placeholder="Buscar por nome, local ou cidade..." value="<?= htmlspecialchars($busca) ?>">
            <button type="submit">Buscar</button>
        </form>
    </section>

    <main class="container">
        <!-- Seção do Mapa Interativo -->
        <section id="mapa-container">
            <h2>Mapa Interativo</h2>
            <div id="mapa"></div>
        </section>

        <!-- Seção de Cards dos Pontos -->
        <section id="locais">
            <h2>Pontos Turísticos</h2>
            <div class="grid-cards">
                <?php if (count($pontos) > 0): ?>
                    <?php foreach ($pontos as $ponto): ?>
                        <div class="card">
                            <img src="<?= htmlspecialchars($ponto['url_imagem'] ?? 'https://via.placeholder.com/400x250?text=Sem+Foto') ?>" alt="<?= htmlspecialchars($ponto['nome']) ?>">
                            <div class="card-body">
                                <h3><?= htmlspecialchars($ponto['nome']) ?></h3>
                                <p><?= htmlspecialchars(mb_strimwidth($ponto['descricao'] ?? '', 0, 100, "...")) ?></p>
                                <span class="endereco">📍 <?= htmlspecialchars($ponto['endereco']) ?></span>
                                <a href="detalhes.php?id=<?= $ponto['id_ponto'] ?>" class="btn">Ver Detalhes</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sem-resultados">Nenhum ponto turístico encontrado.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Leaflet JS para renderizar o mapa -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inicializa o mapa centralizado no Alto Vale do Itajaí (Coordenadas aproximadas de Rio do Sul)
        var map = L.map('mapa').setView([-27.2141, -49.6428], 10);

        // Adiciona a camada de mapa do OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Array PHP para JavaScript contendo os dados dos pontos para os marcadores
        var pontos = <?= json_encode($pontos); ?>;

        // Adiciona marcadores no mapa dinamicamente
        pontos.forEach(function(ponto) {
            if (ponto.latitude && ponto.longitude) {
                var marker = L.marker([ponto.latitude, ponto.longitude]).addTo(map);
                
                var popupContent = `
                    <div style="text-align: center;">
                        <strong>${ponto.nome}</strong><br>
                        <small>${ponto.endereco}</small><br><br>
                        <a href="detalhes.php?id=${ponto.id_ponto}" style="background: #2e7d32; color: #fff; padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 12px;">Ver mais</a>
                    </div>
                `;
                marker.bindPopup(popupContent);
            }
        });
    </script>
</body>
</html>