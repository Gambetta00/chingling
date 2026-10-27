
<?php
session_start();

require_once "conexao_msql.php";


$buscaNome = trim($_GET['busca_nome'] ?? '');
$categoriaBusca = (int)($_GET['categoria_busca'] ?? 0);

$precoMinTexto = trim($_GET['preco_min'] ?? '');
$precoMaxTexto = trim($_GET['preco_max'] ?? '');

$precoMin = ($precoMinTexto !== '' && is_numeric($precoMinTexto))
    ? (float)$precoMinTexto : null;

$precoMax = ($precoMaxTexto !== '' && is_numeric($precoMaxTexto))
    ? (float)$precoMaxTexto : null;

$categorias = $conexao->query(
    "SELECT id, nome FROM categorias ORDER BY nome ASC"
);


// É necessário estar logado.
if (!isset($_SESSION['email'])) {
    header("Location: Login.php");
    exit;
}

$emailUsuario = $_SESSION['email'];

// Token para validar os formulários.
if (empty($_SESSION['csrf_painel'])) {
    $_SESSION['csrf_painel'] = bin2hex(random_bytes(32));
}

// Mensagem de retorno.
if (isset($_SESSION['mensagem_painel'])) {
    $mensagemPainel = $_SESSION['mensagem_painel'];
    unset($_SESSION['mensagem_painel']);
} else {
    $mensagemPainel = "";
}

// Processamento das compras e recargas.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tokenRecebido = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_painel'], $tokenRecebido)) {
        $_SESSION['mensagem_painel'] = "Formulário inválido. Tente novamente.";
        header("Location: Painel.php");
        exit;
    }

    $acao = $_POST['acao'] ?? '';

    // =====================================
    // COMPRAR UM MÓVEL
    // =====================================

    if ($acao === 'comprar') {

        $produtoId = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);

        if (!$produtoId || $produtoId < 1) {
            $_SESSION['mensagem_painel'] = "Produto inválido.";
            header("Location: Painel.php");
            exit;
        }

        $transacaoIniciada = false;

        try {
            $conexao->begin_transaction();
            $transacaoIniciada = true;

            // Bloqueia o saldo do usuário durante a compra.
            $sqlUsuario = "
                SELECT id, saldo
                FROM usuarios
                WHERE email = ?
                FOR UPDATE
            ";

            $stmtUsuario = $conexao->prepare($sqlUsuario);
            $stmtUsuario->bind_param("s", $emailUsuario);
            $stmtUsuario->execute();

            $usuario = $stmtUsuario->get_result()->fetch_assoc();
            $stmtUsuario->close();

            if (!$usuario) {
                throw new Exception("Usuário não encontrado.");
            }

            // Bloqueia o produto para evitar compras simultâneas
            // da última unidade em estoque.
            $sqlProduto = "
                SELECT id, nome, categoria_id, preco,
                       preco_promocional, estoque, ativo
                FROM produtos
                WHERE id = ?
                FOR UPDATE
            ";

            $stmtProduto = $conexao->prepare($sqlProduto);
            $stmtProduto->bind_param("i", $produtoId);
            $stmtProduto->execute();

            $produto = $stmtProduto->get_result()->fetch_assoc();
            $stmtProduto->close();

            if (!$produto || (int)$produto['ativo'] !== 1) {
                throw new Exception("Este móvel não está disponível.");
            }

            if ((int)$produto['estoque'] <= 0) {
                throw new Exception("Este móvel está sem estoque.");
            }

            // Usa o preço promocional quando for válido
            // e inferior ao preço normal.
            $preco = (float)$produto['preco'];
            $precoPromocional = $produto['preco_promocional'];

            if (
                $precoPromocional !== null &&
                (float)$precoPromocional > 0 &&
                (float)$precoPromocional < $preco
            ) {
                $preco = (float)$precoPromocional;
            }

            // Compara valores em centavos para evitar
            // erros comuns de arredondamento monetário.
            $saldoCentavos = (int)round(
                (float)$usuario['saldo'] * 100
            );

            $precoCentavos = (int)round($preco * 100);

            if ($saldoCentavos < $precoCentavos) {
                throw new Exception("Saldo insuficiente para comprar este móvel.");
            }

            // Desconta o saldo.
            $novoSaldo = ($saldoCentavos - $precoCentavos) / 100;

            $sqlSaldo = "
                UPDATE usuarios
                SET saldo = ?
                WHERE id = ?
            ";

            $stmtSaldo = $conexao->prepare($sqlSaldo);
            $stmtSaldo->bind_param(
                "di",
                $novoSaldo,
                $usuario['id']
            );
            $stmtSaldo->execute();
            $stmtSaldo->close();

            // Diminui uma unidade do estoque.
            $sqlEstoque = "
                UPDATE produtos
                SET estoque = estoque - 1
                WHERE id = ?
                  AND estoque > 0
                  AND ativo = 1
            ";

            $stmtEstoque = $conexao->prepare($sqlEstoque);
            $stmtEstoque->bind_param("i", $produtoId);
            $stmtEstoque->execute();

            if ($stmtEstoque->affected_rows !== 1) {
                $stmtEstoque->close();
                throw new Exception("Não foi possível atualizar o estoque.");
            }

            $stmtEstoque->close();

            // Registra a compra e a categoria do móvel.
            $categoriaId = (int)$produto['categoria_id'];

            $sqlCompra = "
                INSERT INTO compras
                    (usuario_email, produto_id, categoria_id,
                     preco_pago, quantidade)
                VALUES (?, ?, ?, ?, 1)
            ";

            $stmtCompra = $conexao->prepare($sqlCompra);
            $stmtCompra->bind_param(
                "siid",
                $emailUsuario,
                $produtoId,
                $categoriaId,
                $preco
            );
            $stmtCompra->execute();
            $stmtCompra->close();

            // Confirma todas as alterações juntas.
            $conexao->commit();
            $transacaoIniciada = false;

            $_SESSION['mensagem_painel'] =
                "Compra realizada com sucesso!";

        } catch (Throwable $erro) {

            if ($transacaoIniciada) {
                $conexao->rollback();
            }

            $_SESSION['mensagem_painel'] =
                $erro instanceof Exception
                ? $erro->getMessage()
                : "Não foi possível concluir a compra.";
        }

        header("Location: Painel.php");
        exit;
    }

    // =====================================
    // RECARREGAR SALDO — MODO DE TESTE
    // =====================================

    

if ($acao === 'recarga') {
    $valorRecarga = filter_input(
        INPUT_POST,
        'valor_recarga',
        FILTER_VALIDATE_FLOAT
    );

    $valoresPermitidos = [50.0, 100.0, 200.0, 500.0];

    if (
        $valorRecarga === false ||
        $valorRecarga === null ||
        !in_array((float)$valorRecarga, $valoresPermitidos, true)
    ) {
        $_SESSION['mensagem_painel'] = "Selecione um valor válido.";
        header("Location: Painel.php");
        exit;
    }

    $emailRecarga = $_SESSION['email'];
    $valorRecarga = (float)$valorRecarga;

    $sqlRecarga = "
        UPDATE usuarios
        SET saldo = saldo + ?
        WHERE email = ?
        LIMIT 1
    ";

    $stmtRecarga = $conexao->prepare($sqlRecarga);

    if (!$stmtRecarga) {
        $_SESSION['mensagem_painel'] =
            "Erro ao preparar a recarga: " . $conexao->error;
    } else {
        $stmtRecarga->bind_param(
            "ds",
            $valorRecarga,
            $emailRecarga
        );

        if (!$stmtRecarga->execute()) {
            $_SESSION['mensagem_painel'] =
                "Erro ao executar a recarga: " . $stmtRecarga->error;
        } elseif ($stmtRecarga->affected_rows === 1) {
            $_SESSION['mensagem_painel'] =
                "Recarga realizada com sucesso!";
        } else {
            // Verifica se o usuário existe na conexão atual.
            $stmtVerifica = $conexao->prepare(
                "SELECT id, saldo FROM usuarios WHERE email = ? LIMIT 1"
            );

            $stmtVerifica->bind_param("s", $emailRecarga);
            $stmtVerifica->execute();
            $resultadoVerifica = $stmtVerifica->get_result();

            if ($usuario = $resultadoVerifica->fetch_assoc()) {
                $_SESSION['mensagem_painel'] =
                    "O usuário foi encontrado, mas o saldo não foi alterado. Saldo atual: R$ " .
                    number_format((float)$usuario['saldo'], 2, ',', '.');
            } else {
                $_SESSION['mensagem_painel'] =
                    "O e-mail da sessão não corresponde a um usuário encontrado.";
            }

            $stmtVerifica->close();
        }

        $stmtRecarga->close();
    }

    header("Location: Painel.php");
    exit;
}


}

// =====================================
// BUSCAR USUÁRIO
// =====================================

$sqlUsuario = "
    SELECT nome, saldo, tipo
    FROM usuarios
    WHERE email = ?
";

$stmtUsuario = $conexao->prepare($sqlUsuario);
$stmtUsuario->bind_param("s", $emailUsuario);
$stmtUsuario->execute();

$usuarioAtual = $stmtUsuario->get_result()->fetch_assoc();
$stmtUsuario->close();

if (!$usuarioAtual) {
    session_destroy();
    header("Location: Login.php");
    exit;
}

// =====================================
// BUSCAR PRODUTOS
// Prioriza categorias mais compradas.
// =====================================


$filtros = ["p.ativo = 1"];
$tipos = "s";
$parametros = [$emailUsuario];

if ($buscaNome !== '') {
    $filtros[] = "p.nome LIKE ?";
    $tipos .= "s";
    $parametros[] = "%" . $buscaNome . "%";
}

if ($categoriaBusca > 0) {
    $filtros[] = "p.categoria_id = ?";
    $tipos .= "i";
    $parametros[] = $categoriaBusca;
}

$expressaoPreco = "
    CASE
        WHEN p.preco_promocional IS NOT NULL
             AND p.preco_promocional > 0
             AND p.preco_promocional < p.preco
        THEN p.preco_promocional
        ELSE p.preco
    END
";

if ($precoMin !== null) {
    $filtros[] = "$expressaoPreco >= ?";
    $tipos .= "d";
    $parametros[] = $precoMin;
}

if ($precoMax !== null) {
    $filtros[] = "$expressaoPreco <= ?";
    $tipos .= "d";
    $parametros[] = $precoMax;
}

$sqlProdutos = "
    SELECT
        p.id,
        p.nome,
        p.descricao,
        p.preco,
        p.preco_promocional,
        p.estoque,
        p.imagem,
        p.destaque,
        c.nome AS categoria,
        (
            SELECT COALESCE(SUM(co.quantidade), 0)
            FROM compras co
            WHERE co.usuario_email = ?
              AND co.categoria_id = p.categoria_id
        ) AS compras_categoria
    FROM produtos p
    LEFT JOIN categorias c ON c.id = p.categoria_id
    WHERE " . implode(" AND ", $filtros) . "
    ORDER BY compras_categoria DESC, p.destaque DESC, p.nome ASC
";

$stmtProdutos = $conexao->prepare($sqlProdutos);

$argumentos = [$tipos];

foreach ($parametros as $i => $valor) {
    $argumentos[] = &$parametros[$i];
}

call_user_func_array([$stmtProdutos, 'bind_param'], $argumentos);

$stmtProdutos->execute();
$resultadoProdutos = $stmtProdutos->get_result();


function escapar($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel - Chingling</title>

    <link rel="stylesheet" href="style2.css">
</head>

<body>

    <header>
        <div class="logo">
            <span>🏠</span>
            <strong>Chingling</strong>
        </div>

        <div class="usuario">
            Olá, <?= escapar($usuarioAtual['nome']) ?>!

            <form action="Login.php" method="POST" style="display:inline;">
                <input
                    type="hidden"
                    name="sinal_clique"
                    value="ativado"
                >

                <button type="submit" class="botao-sair">
                    Sair
                </button>
            </form>
        </div>
    </header>

    <nav>
        <a href="Painel.php">Início</a>
        <a href="Moveis.php">Móveis</a>

        <?php if ($usuarioAtual['tipo'] === 'admin'): ?>
            <a href="AdmMoveis.php">Administração de Móveis</a>
            <a href="AdmUsuarios.php">Administração de Usuários</a>
        <?php endif; ?>
    </nav>

    <main class="painel">

        <div class="topo-painel">

            <section class="boas-vindas">
                <h1>Bem-vindo ao Chingling!</h1>

                <p>
                    Encontre os móveis ideais para sua casa.
                </p>

                <p>
                    Os móveis das categorias que você mais compra
                    aparecem primeiro.
                </p>
            </section>

            <!-- PAINEL LATERAL DE SALDO -->
            <aside class="saldo-caixa">

                <h2>Meu saldo</h2>

                <p class="saldo-valor">
                    R$
                    <?= number_format(
                        (float)$usuarioAtual['saldo'],
                        2,
                        ',',
                        '.'
                    ) ?>
                </p>

                <form method="POST" action="Painel.php">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapar($_SESSION['csrf_painel']) ?>"
                    >

                    <input
                        type="hidden"
                        name="acao"
                        value="recarga"
                    >

                    <label for="valor_recarga">
                        Recarregar saldo
                    </label>

                    <select
                        name="valor_recarga"
                        id="valor_recarga"
                        required
                    >
                        <option value="">Escolha um valor</option>
                        <option value="50">R$ 50,00</option>
                        <option value="100">R$ 100,00</option>
                        <option value="200">R$ 200,00</option>
                        <option value="500">R$ 500,00</option>
                    </select>

                    <button type="submit" class="botao-recarregar">
                        Recarregar
                    </button>

                </form>

                <p class="aviso-recarga">
                    Recarga simulada para testes.
                </p>

            </aside>

        </div>

        <?php if ($mensagemPainel !== ''): ?>
            <div class="mensagem-painel" role="status">
                <?= escapar($mensagemPainel) ?>
            </div>
        <?php endif; ?>

        <section class="secao-produtos">

            <h2>Móveis disponíveis</h2>

            <?php if (count($produtos) === 0): ?>

                <p class="sem-produtos">
                    Nenhum móvel disponível no momento.
                </p>

            <?php else: ?>

                <div class="lista-produtos-painel">
                            
<form method="GET" action="Painel.php" class="pesquisa-painel">

    <div class="campo-pesquisa-painel">
        <label for="busca_nome">Nome do produto</label>
        <input
            type="text"
            id="busca_nome"
            name="busca_nome"
            placeholder="Ex.: Mesa, cadeira..."
            value="<?= htmlspecialchars($buscaNome) ?>"
        >
    </div>

    <div class="campo-pesquisa-painel">
        <label for="categoria_busca">Categoria</label>
        <select id="categoria_busca" name="categoria_busca">
            <option value="0">Todas as categorias</option>

            <?php while ($cat = $categorias->fetch_assoc()): ?>
                <option
                    value="<?= (int)$cat['id'] ?>"
                    <?= $categoriaBusca === (int)$cat['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($cat['nome']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="campo-pesquisa-painel">
        <label for="preco_min">Preço mínimo (R$)</label>
        <input
            type="number"
            id="preco_min"
            name="preco_min"
            min="0"
            step="0.01"
            placeholder="0,00"
            value="<?= htmlspecialchars($precoMinTexto) ?>"
        >
    </div>

    <div class="campo-pesquisa-painel">
        <label for="preco_max">Preço máximo (R$)</label>
        <input
            type="number"
            id="preco_max"
            name="preco_max"
            min="0"
            step="0.01"
            placeholder="Sem limite"
            value="<?= htmlspecialchars($precoMaxTexto) ?>"
        >
    </div>

    <button type="submit" class="botao-pesquisar-painel">
        Pesquisar
    </button>

    <a href="Painel.php" class="botao-limpar-pesquisa">
        Limpar filtros
    </a>

</form>

                    <?php foreach ($produtos as $produto): ?>

                        <?php
                        $precoFinal = (float)$produto['preco'];

                        if (
                            $produto['preco_promocional'] !== null &&
                            (float)$produto['preco_promocional'] > 0 &&
                            (float)$produto['preco_promocional'] < $precoFinal
                        ) {
                            $precoFinal = (float)$produto['preco_promocional'];
                        }

                        $semEstoque = (int)$produto['estoque'] <= 0;
                        ?>

                        <article class="produto-painel-card">

                            <h3>
                                <?= escapar($produto['nome']) ?>
                            </h3>

                            <?php if (!empty($produto['imagem'])): ?>

                                <img
                                    src="<?= escapar($produto['imagem']) ?>"
                                    alt="<?= escapar($produto['nome']) ?>"
                                    class="produto-painel-imagem"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="sem-imagem">
                                    Imagem não disponível
                                </div>

                            <?php endif; ?>

                            <div class="produto-painel-dados">

                                <p>
                                    <strong>Categoria:</strong>
                                    <?= escapar($produto['categoria'] ?? 'Não definida') ?>
                                </p>

                                <p>
                                    <strong>Preço:</strong>

                                    <?php if ($precoFinal < (float)$produto['preco']): ?>

                                        <span class="preco-antigo">
                                            R$
                                            <?= number_format(
                                                (float)$produto['preco'],
                                                2,
                                                ',',
                                                '.'
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                    <strong>
                                        R$
                                        <?= number_format(
                                            $precoFinal,
                                            2,
                                            ',',
                                            '.'
                                        ) ?>
                                    </strong>
                                </p>

                                <p>
                                    <strong>Estoque:</strong>
                                    <?= (int)$produto['estoque'] ?>
                                </p>

                                <p>
                                    <strong>Descrição:</strong>
                                    <?= escapar(
                                        $produto['descricao'] ?: 'Sem descrição.'
                                    ) ?>
                                </p>

                            </div>

                            <?php if ($semEstoque): ?>

                                <button
                                    type="button"
                                    class="botao-comprar"
                                    disabled
                                >
                                    Sem estoque
                                </button>

                            <?php else: ?>

                                <form
                                    method="POST"
                                    action="Painel.php"
                                    class="form-comprar"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= escapar($_SESSION['csrf_painel']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="acao"
                                        value="comprar"
                                    >

                                    <input
                                        type="hidden"
                                        name="produto_id"
                                        value="<?= (int)$produto['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="botao-comprar"
                                    >
                                        Comprar
                                    </button>

                                </form>

                            <?php endif; ?>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>

