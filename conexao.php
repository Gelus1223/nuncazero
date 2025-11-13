<?php
$host = "sql100.infinityfree.com";
$user = "if0_40276199";
$pass = "HuOR2NUm6k8Z";
$db = "if0_40276199_nuncazero";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
