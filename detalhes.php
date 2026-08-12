<?php
require_once "conexao.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT p.*, f.url_imagem 
        FROM pontoturistico p
        LEFT JOIN fotos f ON p.id_fotos = f.id_fotos
        WHERE p.id_ponto = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id);
$stmt->execute();
$ponto = $stmt->fetch();

if (!$ponto) {
    header("Location: index.php");
    exit;
}

// Link direto para rotas no Google Maps usando latitude e longitude
$rota_url = "https://www.google.com/maps/dir/?api=1&destination=" . $ponto['latitude'] . "," . $ponto['longitude'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ponto['nome']) ?> - AltoTuristc</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <h1>Alto<span>Turistc</span></h1>
        </div>
        <nav>
            <a href="index.php">← Voltar ao Início</a>
        </nav>
    </header>

    <main class="container detalhes-container">
        <div class="detalhes-header">
            <h2><?= htmlspecialchars($ponto['nome']) ?></h2>
            <p class="endereco">📍 <?= htmlspecialchars($ponto['endereco']) ?></p>
        </div>

        <div class="detalhes-midia">
            <img src="<?= htmlspecialchars($ponto['url_imagem'] ?? 'https://via.placeholder.com/800x450?text=Sem+Foto') ?>" alt="<?= htmlspecialchars($ponto['nome']) ?>">
        </div>

        <div class="detalhes-info">
            <div class="bloco-info">
                <h3>Descrição</h3>
                <p><?= nl2br(htmlspecialchars($ponto['descricao'] ?? 'Sem descrição disponível.')) ?></p>
            </div>

            <?php if (!empty($ponto['horario_funcionamento'])): ?>
            <div class="bloco-info">
                <h3>🕒 Horário de Funcionamento</h3>
                <p><?= htmlspecialchars($ponto['horario_funcionamento']) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($ponto['informacoes_seguranca'])): ?>
            <div class="bloco-info alerta-seguranca">
                <h3>⚠️ Informações de Segurança e Acesso</h3>
                <p><?= nl2br(htmlspecialchars($ponto['informacoes_seguranca'])) ?></p>
            </div>
            <?php endif; ?>

            <div class="acoes">
                <a href="<?= $rota_url ?>" target="_blank" class="btn btn-rota">🗺️ Como Chegar (Abrir Rota)</a>
            </div>
        </div>
    </main>

</body>
</html>