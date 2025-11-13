<?php
session_start();
include 'conexao.php';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, senha_hash FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($senha, $usuario['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            header("Location: index.php");
            exit();
        } else {
            $mensagem = "Senha incorreta!";
        }
    } else {
        $mensagem = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login - NuncaZero</title>
<style>
body { 
    font-family: Arial; 
    background:#121212; 
    color:#f1f1f1; 
    display:flex; 
    justify-content:center; 
    align-items:center; 
    min-height:100vh; 
    margin:0; 
}

.form-container { 
    background:#1e1e1e; 
    padding:30px; 
    border-radius:10px; 
    width:350px; 
    box-shadow: 0 0 20px rgba(0,200,83,0.5); 
    position:relative;
}

h2 { 
    color:#00c853; 
    text-align:center; 
    margin-bottom:20px; 
}

input { 
    width:100%; 
    padding:10px; 
    margin:10px 0; 
    border-radius:5px; 
    border:1px solid #00c853; 
    background:#121212; 
    color:#f1f1f1; 
}

input[type="submit"] { 
    background:#00c853; 
    color:#121212; 
    font-weight:bold; 
    border:none; 
    cursor:pointer; 
    transition:0.2s;
}

input[type="submit"]:hover { 
    background:#009624; 
}

.mensagem { 
    text-align:center; 
    margin-top:10px; 
    color:#ff5252; 
    font-weight:bold; 
}

a { 
    color:#00c853; 
    text-decoration:none; 
    font-size:14px; 
    display:block; 
    text-align:center; 
    margin-top:10px; 
    transition:0.2s;
}

a:hover { 
    color:#009624; 
}

/* Botão de voltar fixo */
.botao-voltar { 
    position: fixed; 
    top: 10px; 
    left: 10px; 
    padding:8px 12px; 
    background:#00c853; 
    color:#121212; 
    border-radius:5px; 
    text-decoration:none; 
    font-weight:bold; 
    transition:0.2s; 
}

.botao-voltar:hover { 
    background:#009624; 
}
</style>
</head>
<body>

<!-- Botão de voltar -->
<a href="index.php" class="botao-voltar">Voltar</a>

<div class="form-container">
    <h2>Login</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <input type="submit" value="Entrar">
    </form>
    <?php if($mensagem) echo "<div class='mensagem'>$mensagem</div>"; ?>
    <a href="cadastro.php">Não tem conta? Cadastre-se</a>
</div>

</body>
</html>
