<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
include 'conexao.php';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_titular'];
    $numero = $_POST['numero_cartao'];
    $validade = $_POST['validade'];
    $cvv = $_POST['cvv'];
    $usuario_id = $_SESSION['usuario_id'];

    $stmt = $conn->prepare("INSERT INTO cartao (usuario_id,nome_titular,numero_cartao,validade,cvv) VALUES (?,?,?,?,?)");
    $stmt->bind_param("issss", $usuario_id, $nome, $numero, $validade, $cvv);

    if ($stmt->execute()) {
        $mensagem = "Cartão cadastrado com sucesso!";
        header("Location: index.php");
    } else {
        $mensagem = "Erro: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Cartão - NuncaZero</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #121212; color: #f1f1f1; display:flex; justify-content:center; align-items:center; height:100vh; }
        .form-container { background-color: #1e1e1e; padding: 30px; border-radius: 10px; width: 350px; box-shadow: 0 0 20px rgba(0,200,83,0.5); }
        h2 { color: #00c853; text-align:center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #00c853; background-color:#121212; color:#f1f1f1; }
        input[type="submit"] { background-color:#00c853; color:#121212; font-weight:bold; border:none; cursor:pointer; }
        input[type="submit"]:hover { background-color:#009624; }
        .mensagem { text-align:center; margin-top:10px; color:#00c853; }
        a { color: #00c853; text-decoration:none; font-size:14px; display:block; text-align:center; margin-top:10px; }
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
        label { display:block; margin-top:6px; color:#cdeccc; font-size:14px; }
    </style>
</head>
<body>
    <div class="voltar-container">
        <a href="index.php" class="botao-voltar">Voltar</a>
    </div>
    <div class="form-container">
        <h2>Cadastrar Cartão</h2>
        <form method="post" id="form-cartao">
            <input type="text" name="nome_titular" placeholder="Nome do titular" required>
            <!-- máscara aplicada no JS; maxlength leva em conta espaços (ex: 19 => "1111 2222 3333 4444") -->
            <input type="text" id="numero_cartao" name="numero_cartao" placeholder="Número do cartão" inputmode="numeric" maxlength="19" autocomplete="cc-number" required>
            <label for="validade">Validade:</label>
            <input type="date" name="validade" placeholder="Validade" required>
            <!-- CVV: 3 ou 4 dígitos -->
            <input type="text" id="cvv" name="cvv" placeholder="CVV" inputmode="numeric" maxlength="4" autocomplete="cc-csc" required>
            <input type="submit" value="Cadastrar">
        </form>
        <?php if($mensagem) echo "<div class='mensagem'>$mensagem</div>"; ?>
    </div>

<script>
// Formatação máscara de número do cartão: "1111 2222 3333 4444"
// Aceita até 16 dígitos (formatados com espaços => maxlength 19)
const cardInput = document.getElementById('numero_cartao');
cardInput.addEventListener('input', function(e) {
    let v = e.target.value;
    // remove tudo que não é número
    v = v.replace(/\D/g, '');
    // limita a 16 dígitos
    if (v.length > 16) v = v.slice(0, 16);
    // coloca espaços a cada 4 dígitos
    const parts = [];
    for (let i = 0; i < v.length; i += 4) {
        parts.push(v.substring(i, i + 4));
    }
    e.target.value = parts.join(' ');
});

// Máscara para CVV: somente números, 3 ou 4 dígitos
const cvvInput = document.getElementById('cvv');
cvvInput.addEventListener('input', function(e) {
    let v = e.target.value;
    v = v.replace(/\D/g, ''); // só números
    if (v.length > 4) v = v.slice(0,4); // máximo 4
    e.target.value = v;
});

// Antes de enviar, removemos espaços do número do cartão e garantimos que CVV está apenas numérico.
// Também garantimos que o número do cartão enviado tenha entre 13 e 16 dígitos (cartões válidos normalmente)
// e que o CVV tenha 3 ou 4 dígitos — se quiser ignorar a validação extra, pode remover este bloco.
document.getElementById('form-cartao').addEventListener('submit', function(event) {
    // limpa espaços do cartão para enviar só dígitos
    const rawCard = cardInput.value.replace(/\s+/g, '');
    const rawCvv = cvvInput.value.replace(/\s+/g, '');

    // Validações básicas (cliente)
    if (!/^\d{13,16}$/.test(rawCard)) {
        alert('Número do cartão inválido. Deve conter entre 13 e 16 dígitos.');
        event.preventDefault();
        return false;
    }
    if (!/^\d{3,4}$/.test(rawCvv)) {
        alert('CVV inválido. Deve conter 3 ou 4 dígitos.');
        event.preventDefault();
        return false;
    }

    // atribui os valores "limpos" de volta aos inputs para envio ao servidor
    cardInput.value = rawCard;
    cvvInput.value = rawCvv;

    // Nota: se preferir manter formatado no input enviado, comente as duas linhas acima.
});
</script>

</body>
</html>
