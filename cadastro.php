<?php
session_start();
require_once "conexao.php";

$erro = "";
$sucesso = "";

// Se o usuário já estiver logado, redireciona para a página inicial
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, insira um e-mail válido.";
    } else {
        // Verifica se o e-mail já existe no banco de dados
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $erro = "Este e-mail já está cadastrado em nosso sistema.";
        } else {
            // Criptografa a senha para maior segurança
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $dataAtual = date('Y-m-d');

            $sql = "INSERT INTO usuario (nome, email, senha, data_cadastro) VALUES (:nome, :email, :senha, :data_cadastro)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':data_cadastro', $dataAtual);

            if ($stmt->execute()) {
                $sucesso = "Conta criada com sucesso! <a href='login.php'>Clique aqui para fazer login</a>.";
            } else {
                $erro = "Ocorreu um erro ao salvar o cadastro. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - AltoTuristc</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="page-auth">

    <header class="navbar">
        <div class="logo">
            <h1>Alto<span>Turistc</span></h1>
        </div>
        <nav>
            <a href="index.php">Início</a>
            <a href="cadastro.php" class="btn-nav-menu ativo">Cadastrar</a>
            <a href="index.php#mapa">Mapa</a>
            <a href="index.php#locais">Pontos Turísticos</a>
            <a href="login.php" class="btn-login-menu">Entrar</a>
        </nav>
    </header>

    <main class="auth-hero">
        <div class="auth-card">
            <div class="auth-card-header">
                <h2>Junte-se ao AltoTuristc</h2>
                <p>Crie sua conta para favoritar locais, visualizar rotas exclusivas e descobrir o Alto Vale do Itajaí.</p>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="msg erro"><?= $erro ?></div>
            <?php endif; ?>

            <?php if (!empty($sucesso)): ?>
                <div class="msg sucesso"><?= $sucesso ?></div>
            <?php else: ?>

            <form method="POST" action="cadastro.php" class="auth-form">
                <div class="campo">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required placeholder="Digite seu nome completo">
                </div>

                <div class="campo">
                    <label for="email">Endereço de E-mail</label>
                    <input type="email" id="email" name="email" required placeholder="exemplo@email.com">
                </div>

                <div class="campo">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="Crie uma senha de acesso">
                </div>

                <button type="submit" class="btn btn-full">Finalizar Cadastro</button>
            </form>

            <div class="auth-card-footer">
                <p>Já possui uma conta? <a href="login.php">Acesse aqui</a></p>
            </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>