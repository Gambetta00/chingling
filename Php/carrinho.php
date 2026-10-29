<?php
session_start();

//Criar o carrinho
if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

// Produtos disponíveis
$produtos = [
    1 => ["nome" => "Camiseta", "preco" => 50],
    2 => ["nome" => "Calça", "preco" => 100],
    3 => ["nome" => "Tênis", "preco" => 200]
];
// Adicionar produto
if (isset($_POST["adicionar"])) {
    $id = $_POST["id"];

    $_SESSION["carrinho"][$id] =
        ($_SESSION["carrinho"][$id] ?? 0) + 1;
}
// Remover produto
if (isset($_POST["remover"])) {
    $id = $_POST["id"];
    unset($_SESSION["carrinho"][$id]);
}
// Alterar quantidade
if (isset($_POST["alterar"])) {
    $id = $_POST["id"];
    $qtd = (int) $_POST["quantidade"];

    if ($qtd > 0) {
        $_SESSION["carrinho"][$id] = $qtd;
    } else {
        unset($_SESSION["carrinho"][$id]);
    }
}
// Limpar carrinho
if (isset($_POST["limpar"])) {
    $_SESSION["carrinho"] = [];
}

$subtotal = 0;

foreach ($_SESSION["carrinho"] as $id => $qtd) {
    $subtotal += $produtos[$id]["preco"] * $qtd;
}
// Calcular desconto
$desconto = 0;

if ($subtotal >= 200) {
    
    $desconto = $subtotal * 0.10;
}

// Calcular total
$total = $subtotal - $desconto;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
</head>
<body>
<h1>Carrinho de Compras</h1>

<h2>Produtos disponíveis</h2>