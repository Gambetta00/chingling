<?php
session_start();

// ==========================================
// 1. CRIAR CARRINHO POR USUÁRIO
// ==========================================

// Simulação de usuário logado
$usuarioId = $_SESSION["usuario_id"] ?? "visitante";

// Criar lista de carrinhos
if (!isset($_SESSION["carrinhos"])) {
    $_SESSION["carrinhos"] = [];
}

// Criar carrinho do usuário
if (!isset($_SESSION["carrinhos"][$usuarioId])) {
    $_SESSION["carrinhos"][$usuarioId] = [];
}

// Acessar carrinho do usuário
$carrinho = &$_SESSION["carrinhos"][$usuarioId];

$produtos = [
    1 => [
        "nome" => "Sofá",
        "preco" => 700,
        "estoque" => 1
    ],

    2 => [
        "nome" => "Mesa de Jantar",
        "preco" => 600,
        "estoque" => 1
    ],

    3 => [
        "nome" => "Cadeira",
        "preco" => 300,
        "estoque" => 1
    ],

    4 => [
        "nome" => "Guarda-Roupa",
        "preco" => 1500,
        "estoque" => 1
    ],

    5 => [
        "nome" => "Estante",
        "preco" => 450,
        "estoque" => 1
    ]
];

$mensagem = "";

// ==========================================
// 2. ADICIONAR ITEM
// ==========================================

if (isset($_POST["adicionar"])) {

    $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);

    if ($id !== false && $id !== null && isset($produtos[$id])) {

        $quantidadeAtual = $carrinho[$id] ?? 0;

        if ($quantidadeAtual < $produtos[$id]["estoque"]) {

            $carrinho[$id] = $quantidadeAtual + 1;

            $mensagem = "Produto adicionado ao carrinho!";

        } else {

            $mensagem = "Estoque insuficiente!";
        }

    } else {

        $mensagem = "Produto inválido!";
    }
}

// ==========================================
// 3. ALTERAR QUANTIDADE
// ==========================================

elseif (isset($_POST["alterar"])) {

    $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);

    $qtd = filter_var($_POST["quantidade"] ?? null, FILTER_VALIDATE_INT);

    if ($id !== false && $id !== null &&
        isset($produtos[$id]) &&
        $qtd !== false && $qtd !== null) {

        if ($qtd > 0 && $qtd <= $produtos[$id]["estoque"]) {

            $carrinho[$id] = $qtd;

            $mensagem = "Quantidade alterada com sucesso!";

        } elseif ($qtd == 0) {

            unset($carrinho[$id]);

            $mensagem = "Produto removido do carrinho!";

        } else {

            $mensagem = "Quantidade inválida ou maior que o estoque!";
        }

    } else {

        $mensagem = "Dados inválidos!";
    }
}

// ==========================================
// 4. REMOVER ITEM
// ==========================================

elseif (isset($_POST["remover"])) {

    $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);

    if ($id !== false && $id !== null && isset($carrinho[$id])) {

        unset($carrinho[$id]);

        $mensagem = "Produto removido do carrinho!";

    } else {

        $mensagem = "Produto não encontrado no carrinho!";
    }
}

// ==========================================
// 8. VALIDAR ESTOQUE
// ==========================================

elseif (isset($_POST["validar_estoque"])) {

    $estoqueValido = true;

    foreach ($carrinho as $id => $qtd) {

        if (!isset($produtos[$id]) ||
            $qtd > $produtos[$id]["estoque"]) {

            $estoqueValido = false;
            break;
        }
    }

    if ($estoqueValido) {

        $mensagem = "Estoque validado com sucesso!";

    } else {

        $mensagem = "Existem produtos sem estoque suficiente!";
    }
}

// ==========================================
// 9. ATUALIZAR PREÇOS
// ==========================================

elseif (isset($_POST["atualizar_precos"])) {

    // Os preços são consultados novamente no catálogo.
    // O subtotal, desconto e total serão recalculados abaixo.

    $mensagem = "Preços atualizados com sucesso!";
}

// ==========================================
// 10. ESVAZIAR CARRINHO
// ==========================================

elseif (isset($_POST["limpar"])) {

    $carrinho = [];

    $mensagem = "Carrinho esvaziado com sucesso!";
}

// ==========================================
// 5. CALCULAR SUBTOTAL
// ==========================================

$subtotal = 0;

foreach ($carrinho as $id => $qtd) {

    if (isset($produtos[$id])) {

        $subtotal += $produtos[$id]["preco"] * $qtd;
    }
}

// ==========================================
// 6. CALCULAR DESCONTOS
// ==========================================

$desconto = 0;

if ($subtotal >= 200) {

    $desconto = $subtotal * 0.10;
}

// ==========================================
// 7. CALCULAR TOTAL
// ==========================================

$total = $subtotal - $desconto;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Carrinho de Compras</title>

</head>

<body>

<h1>Carrinho de Compras</h1>

<hr>

<!-- MENSAGENS -->

<?php if ($mensagem != "") { ?>

    <p>
        <?php echo htmlspecialchars($mensagem, ENT_QUOTES, "UTF-8"); ?>
    </p>

<?php } ?>

<!-- PRODUTOS DISPONÍVEIS -->

<h2>Produtos disponíveis</h2>

<?php foreach ($produtos as $id => $produto) { ?>

    <h3>
        <?php echo htmlspecialchars($produto["nome"], ENT_QUOTES, "UTF-8"); ?>
    </h3>

    <p>
        Preço:
        R$ <?php echo number_format($produto["preco"], 2, ",", "."); ?>
    </p>

    <p>
        Estoque disponível:
        <?php echo $produto["estoque"]; ?>
    </p>

    <form method="POST">

        <input type="hidden" name="id"
            value="<?php echo $id; ?>">

        <button type="submit" name="adicionar"
            <?php echo $produto["estoque"] == 0 ? "disabled" : ""; ?>>

            Adicionar ao carrinho

        </button>

    </form>

    <hr>

<?php } ?>

<!-- MEU CARRINHO -->

<h2>Meu carrinho</h2>

<?php if (empty($carrinho)) { ?>

    <p>Seu carrinho está vazio!</p>

<?php } else { ?>

    <?php foreach ($carrinho as $id => $qtd) { ?>

        <?php if (isset($produtos[$id])) { ?>

            <h3>
                <?php echo htmlspecialchars($produtos[$id]["nome"], ENT_QUOTES, "UTF-8"); ?>
            </h3>

            <p>
                Preço:
                R$ <?php echo number_format(
                    $produtos[$id]["preco"], 2, ",", "."
                ); ?>
            </p>

            <p>
                Quantidade:
                <?php echo $qtd; ?>
            </p>

            <p>
                Subtotal do produto:
                R$ <?php echo number_format(
                    $produtos[$id]["preco"] * $qtd,
                    2, ",", "."
                ); ?>
            </p>

            <!-- ALTERAR QUANTIDADE -->

            <form method="POST">

                <input type="hidden" name="id"
                    value="<?php echo $id; ?>">

                <input type="number"
                    name="quantidade"
                    value="<?php echo $qtd; ?>"
                    min="0"
                    max="<?php echo $produtos[$id]["estoque"]; ?>"
                    required>

                <button type="submit" name="alterar">

                    Alterar quantidade

                </button>

            </form>

            <!-- REMOVER PRODUTO -->

            <form method="POST">

                <input type="hidden" name="id"
                    value="<?php echo $id; ?>">

                <button type="submit" name="remover">

                    Remover produto

                </button>

            </form>

            <hr>

        <?php } ?>

    <?php } ?>

<?php } ?>

<!-- RESUMO DA COMPRA -->

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

<hr>

<!-- VALIDAR ESTOQUE -->

<form method="POST">

    <button type="submit" name="validar_estoque">

        Validar estoque

    </button>

</form>

<!-- ATUALIZAR PREÇOS -->

<form method="POST">

    <button type="submit" name="atualizar_precos">

        Atualizar preços

    </button>

</form>

<!-- ESVAZIAR CARRINHO -->

<form method="POST">

    <button type="submit" name="limpar">

        Esvaziar carrinho

    </button>

</form>

</body>
</html> 