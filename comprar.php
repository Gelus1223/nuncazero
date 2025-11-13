<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$mensagem = '';
$conta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Busca a conta selecionada
$stmt = $conn->prepare("SELECT c.id, j.nome AS jogo, c.descricao, c.progresso, c.preco 
                        FROM contas_vend c
                        JOIN jogo j ON c.jogo_id = j.id
                        WHERE c.id = ?");
$stmt->bind_param("i",$conta_id);
$stmt->execute();
$res = $stmt->get_result();
$conta = $res->fetch_assoc();

if(!$conta){
    die("Conta não encontrada.");
}

// Busca cartões do usuário
$cartoes = $conn->query("SELECT * FROM cartao WHERE usuario_id=$usuario_id");

// Processa compra
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $metodo = $_POST['metodo'];
    if($metodo == 'cartao'){
        $cartao_id = $_POST['cartao_id'];
        if($cartao_id == 'novo'){
            // Inserir novo cartão
            $nome = $_POST['nome_titular'];
            $numero = $_POST['numero_cartao'];
            $validade = $_POST['validade'];
            $cvv = $_POST['cvv'];

            $stmt = $conn->prepare("INSERT INTO cartao (usuario_id,nome_titular,numero_cartao,validade,cvv) VALUES (?,?,?,?,?)");
            $stmt->bind_param("issss",$usuario_id,$nome,$numero,$validade,$cvv);
            $stmt->execute();
            $cartao_id = $conn->insert_id;
        }
        $mensagem = "Compra realizada com cartão com sucesso!";
    } else {
        $mensagem = "Compra realizada via Pix com sucesso!";
    }

    // Apagar a conta da venda
    $stmt = $conn->prepare("DELETE FROM contas_vend WHERE id=?");
    $stmt->bind_param("i",$conta_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Comprar Conta - NuncaZero</title>
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

p { 
    margin:5px 0; 
}

input, select { 
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

#cartaoEscolha, #novoCartao { 
    margin-top:10px; 
}

#novoCartao { 
    display:none; 
}

label { 
    display:block; 
    margin-top:10px; 
}

.botao-cadastrar { 
    display:inline-block; 
    margin-top:10px; 
    padding:8px 12px; 
    background:#00c853; 
    color:#121212; 
    border-radius:5px; 
    text-decoration:none; 
    font-weight:bold; 
    transition:0.2s; 
}

.botao-cadastrar:hover { 
    background:#009624; 
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('input[name="metodo"]').change(function(){
        if($(this).val()=='cartao'){
            $('#cartaoEscolha').show();
        } else {
            $('#cartaoEscolha').hide();
            $('#novoCartao').hide();
        }
    });

    $('#cartao_id').change(function(){
        if($(this).val()=='novo'){
            $('#novoCartao').show();
        } else {
            $('#novoCartao').hide();
        }
    });
});
</script>
</head>
<body>

<!-- Botão de voltar -->
<a href="index.php" class="botao-voltar">Voltar</a>

<div class="container">
    <h2>Comprar Conta</h2>
    <p><b>Jogo:</b> <?= htmlspecialchars($conta['jogo']) ?></p>
    <p><b>Descrição:</b> <?= htmlspecialchars($conta['descricao']) ?></p>
    <p><b>Progresso:</b> <?= $conta['progresso'] ?>%</p>
    <p><b>Preço:</b> R$ <?= number_format($conta['preco'],2,',','.') ?></p>

    <form method="post">
        <label>Escolha o método de pagamento:</label>
        <input type="radio" name="metodo" value="pix" required> Pix
        <input type="radio" name="metodo" value="cartao"> Cartão

        <div id="cartaoEscolha">
            <label>Selecione um cartão:</label>
            <select name="cartao_id" id="cartao_id">
                <?php if($cartoes->num_rows>0): ?>
                    <?php while($c=$cartoes->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome_titular']) ?> - <?= substr($c['numero_cartao'],-4) ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
                <option value="novo">Cadastrar novo cartão</option>
            </select>

            <div id="novoCartao">
                <input type="text" name="nome_titular" placeholder="Nome do Titular">
                <input type="text" name="numero_cartao" placeholder="Número do Cartão" maxlength="16">
                <input type="month" name="validade" placeholder="Validade">
                <input type="text" name="cvv" placeholder="CVV" maxlength="3">
            </div>

            <!-- Botão para ir para a tela de cadastrar cartão -->
            <a href="cartao.php" class="botao-cadastrar">Cadastrar Cartão</a>
        </div>

        <input type="submit" value="Finalizar Compra">
    </form>

    <?php if($mensagem) echo "<div class='mensagem'>$mensagem</div>"; ?>
</div>

</body>
</html>
