<?php
// cadastro.php
session_start();
include 'conexao.php';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuario (nome, email, cpf, senha_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $cpf, $senha);

    if ($stmt->execute()) {
        $mensagem = "Cadastro realizado com sucesso!";
        header("Location: login.php");
    } else {
        $mensagem = "Erro: E-mail ou CPF já cadastrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro - NuncaZero</title>
<style>
body {
    font-family: Arial, sans-serif;
    background:#121212;
    color:#f1f1f1;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    margin:0;
}
.container {
    background:#1e1e1e;
    padding:30px 40px;
    border:1px solid #00c853;
    border-radius:10px;
    width:350px;
    box-shadow: 0 0 20px rgba(0,200,83,0.5);
}
h2 {
    color:#00c853;
    text-align:center;
    margin-bottom:20px;
}
input {
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:5px;
    border:1px solid #00c853;
    background:#121212;
    color:#f1f1f1;
}
input[type="submit"] {
    background:#00c853;
    color:#121212;
    font-weight:bold;
    cursor:pointer;
    border:none;
    transition:0.2s;
}
input[type="submit"]:hover {
    background:#009624;
}
.mensagem {
    text-align:center;
    margin-top:10px;
    color:#00c853;
    font-weight:bold;
}
.botao-voltar {
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
.voltar-container {
    position:fixed;
    top:10px;
    left:10px;
}
</style>
</head>
<body>

<div class="voltar-container">
    <a href="index.php" class="botao-voltar">Voltar</a>
</div>

<div class="container">
    <h2>Cadastro de Usuário</h2>
    <form method="post" onsubmit="return validarEmail()">
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="email" id="email" name="email" placeholder="E-mail" required>
        <input type="text" id="cpf" name="cpf" placeholder="CPF (somente números)" maxlength="14" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <input type="submit" value="Cadastrar">
    </form>
    <?php if($mensagem) echo "<div class='mensagem'>$mensagem</div>"; ?>
</div>

<!-- Script de máscara e validação -->
<script>
// Máscara CPF: 111.111.111-11
document.getElementById('cpf').addEventListener('input', function(e) {
    let valor = e.target.value.replace(/\D/g, ''); // Remove tudo que não é número
    if (valor.length > 11) valor = valor.slice(0, 11);
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = valor;
});

// Validação simples de e-mail
function validarEmail() {
    const email = document.getElementById('email').value;
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regex.test(email)) {
        alert('Por favor, insira um e-mail válido (ex: exemplo@gmail.com)');
        return false;
    }
    return true;
}
</script>

</body>
</html>
