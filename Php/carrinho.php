<?php
session_start();

// Criar o carrinho
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

    if (isset($produtos[$id])) {
        $_SESSION["carrinho"][$id] =
            ($_SESSION["carrinho"][$id] ?? 0) + 1;
    }
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

    if (isset($produtos[$id])) {
        if ($qtd > 0) {
            $_SESSION["carrinho"][$id] = $qtd;
        } else {
            unset($_SESSION["carrinho"][$id]);
        }
    }
}

// Limpar carrinho
if (isset($_POST["limpar"])) {
    $_SESSION["carrinho"] = [];
}

// Calcular subtotal
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

<?php foreach ($produtos as $id => $produto) { ?>

    <p>
        <?php echo $produto["nome"]; ?>
        - R$ <?php echo number_format($produto["preco"], 2, ",", "."); ?>
    </p>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="adicionar">
            Adicionar ao carrinho
        </button>
    </form>

<?php } ?>

<hr>

<h2>Meu carrinho</h2>

<?php if (empty($_SESSION["carrinho"])) { ?>

    <p>Seu carrinho está vazio!</p>

<?php } else { ?>

    <?php foreach ($_SESSION["carrinho"] as $id => $qtd) { ?>

        <h3><?php echo $produtos[$id]["nome"]; ?></h3>

        <p>
            Preço: R$
            <?php echo number_format($produtos[$id]["preco"], 2, ",", "."); ?>
        </p>

        <p>Quantidade: <?php echo $qtd; ?></p>

        <p>
            Subtotal:
            R$ <?php echo number_format($produtos[$id]["preco"] * $qtd, 2, ",", "."); ?>
        </p>

        <!-- Alterar quantidade -->
        <form method="POST">

            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <input type="number" name="quantidade"
                value="<?php echo $qtd; ?>" min="0">

            <button type="submit" name="alterar">
                Alterar quantidade
            </button>

        </form>

        <!-- Remover produto -->
        <form method="POST">

            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <button type="submit" name="remover">
                Remover produto
            </button>

        </form>

        <hr>

    <?php } ?>

<?php } ?>

<!-- Valores da compra -->

<h2>Resumo da compra</h2>

<p>
    Subtotal:
    R$ <?php echo number_format($subtotal, 2, ",", "."); ?>
</p>

<p>
    Desconto:
    R$ <?php echo number_format($desconto, 2, ",", "."); ?>
</p>

<h3>
    Total:
    R$ <?php echo number_format($total, 2, ",", "."); ?>
</h3>

<!-- Limpar carrinho -->

<form method="POST">

    <button type="submit" name="limpar">
        Limpar carrinho
    </button>

</form>

</body>
</html>



Entenda as partes novas