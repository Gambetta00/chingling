<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="Login.php" method="POST">
    <!-- Esse campo oculto envia o "sinal" -->
    <input type="hidden" name="sinal_clique" value="ativado">
    
    <button type="submit">Logout</button>
</form>
<form action="AdmMoveis.php" method="POST">
    <!-- Esse campo oculto envia o "sinal" -->
    <input type="hidden" name="clique" value="ativado">
    
    <button type="submit">Administrar produtos (ADM)</button>
</form>
</body>
</html>

<?php
session_start();

$email = $_SESSION['email'];

require_once "conexao_msql.php";

$sql = "SELECT nome FROM usuarios WHERE email = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$stmt->store_result();

$stmt->bind_result($nome);

$stmt->fetch();

echo "Seja bem vindo $nome !";

?>