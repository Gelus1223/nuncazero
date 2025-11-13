<?php
session_start();
/*if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}*/
include 'conexao.php';

$logado = isset($_SESSION['usuario_id']);
// Busca contas de acordo com a pesquisa
$busca = "";
if (isset($_GET['busca'])) {
    $busca = "%" . $_GET['busca'] . "%";
    $stmt = $conn->prepare(
        "SELECT c.id, j.nome AS jogo, c.descricao, c.progresso, c.preco
         FROM contas_vend c
         JOIN jogo j ON c.jogo_id = j.id
         WHERE j.nome LIKE ?
         ORDER BY c.id DESC"
    );
    $stmt->bind_param("s", $busca);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    // Se não houver busca, mostrar todas as contas
    $resultado = $conn->query(
        "SELECT c.id, j.nome AS jogo, c.descricao, c.progresso, c.preco
         FROM contas_vend c
         JOIN jogo j ON c.jogo_id = j.id
         ORDER BY c.id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pesquisar Contas - NuncaZero</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #121212;
            color: #f1f1f1;
        }

        /* Menu lateral */
        .sidebar {
            width: 220px;
            background-color: #1e1e1e;
            display: flex;
            flex-direction: column;
            padding: 20px;
            box-shadow: 2px 0 15px rgba(0, 200, 83, 0.5);
        }

        .sidebar h2 {
            color: #00c853;
            margin-bottom: 20px;
            text-align: center;
        }

        .sidebar a {
            color: #00c853;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
            transition: 0.2s;
            display: block;
            font-weight: bold;
        }

        .sidebar a:hover {
            background-color: #00c853;
            color: #121212;
        }

        .sidebar a.logout {
            color: #ff5252;
            border: 1px solid #ff5252;
        }

        .sidebar a.logout:hover {
            background-color: #ff5252;
            color: #fff;
        }
        
        .sidebar img{
            margin-left: 35px;
            display: flex;        /* permite alinhamento como emoji/texto */
  			width: 150px;                 /* um pouco maior que um emoji (1em = tamanho do texto atual) */
  			height: auto;                 /* preserva proporção */
  			align-items: center;    /* alinha melhor com a linha de texto (ajuste fino) */
			justify-content: center;
  			/* efeito neon / brilho */
  			filter:
    		drop-shadow(0 0 6px rgba(130,255,100,0.9))   /* glow principal verde */
    		drop-shadow(0 0 14px rgba(160,100,255,0.6)); /* halo roxo secundário */
        }

        /* Conteúdo principal */
        .main-content {
            flex: 1;
            padding: 40px;
        }

        .main-content h1 {
            color: #00c853;
            margin-bottom: 20px;
        }

        /* Pesquisa */
        .search-form input[type="text"] {
            width: 300px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #00c853;
            margin-right: 8px;
        }

        .search-form input[type="submit"] {
            padding: 8px 12px;
            background-color: #00c853;
            color: #121212;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-form input[type="submit"]:hover {
            background-color: #009624;
        }

        /* Cards das contas */
        .conta-card {
            background-color: #1e1e1e;
            border: 1px solid #00c853;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .conta-card h3 {
            margin: 0 0 10px 0;
            color: #00c853;
        }

        .conta-card p {
            margin: 5px 0;
        }

        .conta-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #00c853;
            color: #121212;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .conta-card a:hover {
            background-color: #009624;
        }
        
        hr{
            border-color: #00c853;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <img src="logo.png">
        <h2>NuncaZero</h2>
        <a href="index.php">🏠 Menu Principal</a>
    <?php if ($logado): ?>
            <a href="cadastrar_conta.php">📤 Cadastrar Conta</a>
            <a href="cartao.php">💳 Cadastrar Cartão</a>
            <a href="logout.php" class="logout">🚪 Sair</a>
        <?php else: ?>
            <a href="login.php">🔑 Login</a>
        <?php endif; ?>
    </div>
    
    <div class="main-content">
        <h1>Pesquisar Contas à Venda</h1>
        
        <form method="get" class="search-form">
            <input type="text" name="busca" placeholder="Digite o nome do jogo" value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
            <input type="submit" value="Pesquisar">
        </form>
		<hr>
        <br>

        <?php if ($resultado->num_rows > 0): ?>
            <?php while ($linha = $resultado->fetch_assoc()): ?>
                <div class="conta-card">
                    <h3><?= htmlspecialchars($linha['jogo']) ?></h3>
                    <p><b>Descrição:</b> <?= htmlspecialchars($linha['descricao']) ?></p>
                    <p><b>Progresso:</b> <?= $linha['progresso'] ?>%</p>
                    <p><b>Preço:</b> R$ <?= number_format($linha['preco'], 2, ',', '.') ?></p>
                     <?php if ($logado): ?>
                        <a href="comprar.php?id=<?= $linha['id'] ?>">Comprar</a>
                    <?php else: ?>
                        <p style="color:#ff5252;font-weight:bold;">Faça login para comprar</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma conta encontrada.</p>
        <?php endif; ?>
    </div>

</body>
</html>
