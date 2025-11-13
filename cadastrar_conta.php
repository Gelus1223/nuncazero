<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';

// Lista de jogos mais jogados (pode substituir com consulta ao banco depois)
$jogos = ["FIFA 24", "League of Legends", "Valorant", "Minecraft", "GTA V", "Call of Duty"];

// Processa cadastro da conta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jogo = $_POST['jogo'];
    $descricao = $_POST['descricao'];
    $progresso = intval($_POST['progresso']);
    $preco = floatval($_POST['preco']);

    // Busca id do jogo na tabela jogo ou cria se não existir
    $stmt = $conn->prepare("SELECT id FROM jogo WHERE nome=?");
    $stmt->bind_param("s", $jogo);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $jogo_id = $res->fetch_assoc()['id'];
    } else {
        $stmt = $conn->prepare("INSERT INTO jogo (nome) VALUES (?)");
        $stmt->bind_param("s", $jogo);
        $stmt->execute();
        $jogo_id = $conn->insert_id;
    }

    // Insere conta na tabela contas_vend
    $stmt = $conn->prepare("INSERT INTO contas_vend (usuario_id,jogo_id,descricao,progresso,preco) VALUES (?,?,?,?,?)");
    $stmt->bind_param("iisid", $usuario_id, $jogo_id, $descricao, $progresso, $preco);

    if ($stmt->execute()) {
        $mensagem = "Conta cadastrada com sucesso!";
        header("Location: index.php");
    } else {
        $mensagem = "Erro ao cadastrar a conta.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Conta - NuncaZero</title>
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

.container { 
    background:#1e1e1e; 
    padding:30px; 
    border:1px solid #00c853; 
    border-radius:10px; 
    width:400px; 
    position:relative; 
}

h2 { 
    color:#00c853; 
    text-align:center; 
    margin-bottom:20px; 
}

input, select, textarea { 
    width:100%; 
    padding:10px; 
    margin:8px 0; 
    border-radius:5px; 
    border:1px solid #00c853; 
    background:#121212; 
    color:#f1f1f1; 
}

textarea { resize:none; }

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

<a href="index.php" class="botao-voltar">Voltar</a>

<div class="container">
    <h2>Cadastrar Conta de Jogo</h2>
    <form method="post">
        <label>Jogo:</label>
        <select name="jogo" required>
            <option value="">Selecione...</option>
            <?php foreach($jogos as $j): ?>
                <option value="<?= htmlspecialchars($j) ?>"><?= htmlspecialchars($j) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Descrição:</label>
        <textarea name="descricao" rows="4" placeholder="Descreva sua conta" required></textarea>

        <label>Progresso (%):</label>
        <input type="number" name="progresso" min="0" max="100" placeholder="0 a 100" required>

        <label>Preço (R$):</label>
        <input type="number" step="0.01" name="preco" placeholder="Ex: 49.90" required>

        <input type="submit" value="Cadastrar Conta">
    </form>

    <?php if($mensagem) echo "<div class='mensagem'>$mensagem</div>";?>
</div>

</body>
</html>
