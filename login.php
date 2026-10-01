<?php
session_start();
require_once "conexao.php";

$erro = "";

// Se já estiver logado, redireciona para a home
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $erro = "Preencha o e-mail e a senha.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch();

        // Verifica se usuário existe e se a senha bate (suporta hashes modernos ou senhas diretas)
        if ($usuario && (password_verify($senha, $usuario['senha']) || $senha === $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];

            header("Location: index.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AltoTuristc</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <h1>Alto<span>Turistc</span></h1>
        </div>
        <nav>
            <a href="index.php">Início</a>
            <a href="cadastro.php">Cadastrar-se</a>
        </nav>
    </header>

    <main class="container auth-container">
        <h2>Entrar na sua Conta</h2>

        <?php if (!empty($erro)): ?>
            <div class="msg erro"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="auth-form">
            <div class="campo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required placeholder="seu@email.com">
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required placeholder="Sua senha">
            </div>

            <button type="submit" class="btn">Entrar</button>
        </form>

        <p class="auth-link">Ainda não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
    </main>

</body>
</html>