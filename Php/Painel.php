<?php
session_start();
require_once "conexao_msql.php";

if (empty($_SESSION['email'])) {
    header("Location: Login.php");
    exit;
}

$emailUsuario = $_SESSION['email'];

if (!isset($_SESSION['carrinho']) || !is_array($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function caminhoImagem($imagem) {
    return trim((string)$imagem);
}

function definirMensagem($texto, $tipo = 'erro') {
    $_SESSION['mensagem_painel'] = $texto;
    $_SESSION['mensagem_painel_tipo'] = $tipo;
}

$sql = "SELECT tipo FROM usuarios WHERE email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $emailUsuario);
$stmt->execute();
$resultadoTipo = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$resultadoTipo) {
    session_destroy();
    header("Location: Login.php");
    exit;
}

$tipo = $resultadoTipo['tipo'];
$_SESSION['tipo'] = $tipo;

$buscaNome = trim($_GET['busca_nome'] ?? '');
$categoriaBusca = (int)($_GET['categoria_busca'] ?? 0);

$precoMinTexto = trim($_GET['preco_min'] ?? '');
$precoMaxTexto = trim($_GET['preco_max'] ?? '');

$precoMin = ($precoMinTexto !== '' && is_numeric($precoMinTexto))
    ? (float)$precoMinTexto : null;

$precoMax = ($precoMaxTexto !== '' && is_numeric($precoMaxTexto))
    ? (float)$precoMaxTexto : null;

if (empty($_SESSION['csrf_painel'])) {
    $_SESSION['csrf_painel'] = bin2hex(random_bytes(32));
}

$mensagemPainel = $_SESSION['mensagem_painel'] ?? '';
$tipoMensagem = $_SESSION['mensagem_painel_tipo'] ?? 'erro';

unset($_SESSION['mensagem_painel']);
unset($_SESSION['mensagem_painel_tipo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tokenRecebido = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_painel'], $tokenRecebido)) {
        definirMensagem("Formulário inválido. Tente novamente.");
        header("Location: Painel.php");
        exit;
    }

    $acao = $_POST['acao'] ?? '';

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

            ksort($carrinho);

            $produtosCompra = [];
            $totalCentavos = 0;

            foreach ($carrinho as $id => $quantidade) {

                $id = (int)$id;
                $quantidade = (int)$quantidade;

                if ($id < 1 || $quantidade < 1) {
                    throw new Exception("Há um produto inválido no carrinho.");
                }

                $sqlProduto = "
                    SELECT id, nome, categoria_id, preco,
                           preco_promocional, estoque, ativo
                    FROM produtos
                    WHERE id = ?
                    FOR UPDATE
                ";

                $stmtProduto = $conexao->prepare($sqlProduto);
                $stmtProduto->bind_param("i", $id);
                $stmtProduto->execute();

                $produto = $stmtProduto->get_result()->fetch_assoc();
                $stmtProduto->close();

                if (!$produto || (int)$produto['ativo'] !== 1) {
                    throw new Exception("Um dos móveis do carrinho não está mais disponível.");
                }

                if ((int)$produto['estoque'] < $quantidade) {
                    throw new Exception(
                        "Estoque insuficiente para o móvel: " . $produto['nome'] . "."
                    );
                }

                $preco = (float)$produto['preco'];

                if (
                    $produto['preco_promocional'] !== null &&
                    (float)$produto['preco_promocional'] > 0 &&
                    (float)$produto['preco_promocional'] < $preco
                ) {
                    $preco = (float)$produto['preco_promocional'];
                }

                $precoCentavos = (int)round($preco * 100);
                $subtotalCentavos = $precoCentavos * $quantidade;

                $totalCentavos += $subtotalCentavos;

                $produto['quantidade_compra'] = $quantidade;
                $produto['preco_final'] = $preco;
                $produtosCompra[] = $produto;
            }

            $saldoCentavos = (int)round((float)$usuario['saldo'] * 100);

            if ($saldoCentavos < $totalCentavos) {
                throw new Exception(
                    "Saldo insuficiente! Seu saldo é R$ " .
                    number_format($saldoCentavos / 100, 2, ',', '.') .
                    " e o total do carrinho é R$ " .
                    number_format($totalCentavos / 100, 2, ',', '.') . "."
                );
            }

            $novoSaldo = ($saldoCentavos - $totalCentavos) / 100;

            $sqlSaldo = "
                UPDATE usuarios
                SET saldo = ?
                WHERE id = ?
            ";

            $stmtSaldo = $conexao->prepare($sqlSaldo);
            $stmtSaldo->bind_param("di", $novoSaldo, $usuario['id']);
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
                $stmtEstoque->bind_param("iii", $quantidade, $id, $quantidade);
                $stmtEstoque->execute();

                if ($stmtEstoque->affected_rows !== 1) {
                    $stmtEstoque->close();
                    throw new Exception(
                        "Não foi possível atualizar o estoque de " .
                        $produto['nome'] . "."
                    );
                }

                $stmtEstoque->close();

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

            $conexao->commit();
            $transacaoIniciada = false;

            $_SESSION['carrinho'] = [];

            definirMensagem(
                "Compra realizada com sucesso! Total: R$ " .
                number_format($totalCentavos / 100, 2, ',', '.'),
                'sucesso'
            );

        } catch (Throwable $erro) {

            if ($transacaoIniciada) {
                $conexao->rollback();
            }

            if ($erro instanceof Exception) {
                definirMensagem($erro->getMessage());
            } else {
                definirMensagem("Ocorreu um erro ao processar sua compra. Nenhum produto foi comprado.");
            }
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
            definirMensagem("Selecione um valor válido.");
            header("Location: Painel.php");
            exit;
        }

        $valorRecarga = (float)$valorRecarga;

        $sqlRecarga = "
            UPDATE usuarios
            SET saldo = saldo + ?
            WHERE email = ?
            LIMIT 1
        ";

        $stmtRecarga = $conexao->prepare($sqlRecarga);
        $stmtRecarga->bind_param("ds", $valorRecarga, $emailUsuario);

        if ($stmtRecarga->execute() && $stmtRecarga->affected_rows === 1) {
            definirMensagem("Recarga realizada com sucesso!", 'sucesso');
        } else {
            definirMensagem("Não foi possível realizar a recarga.");
        }

        $stmtRecarga->close();

        header("Location: Painel.php");
        exit;
    }

}

$sqlUsuario = "
    SELECT nome, saldo, tipo, imagem
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

$categorias = $conexao->query(
    "SELECT id, nome FROM categorias ORDER BY nome ASC"
);

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

$produtos = [];

while ($produto = $resultadoProdutos->fetch_assoc()) {
    $produtos[] = $produto;
}

$stmtProdutos->close();

$itensCarrinho = [];
$totalCarrinhoCentavos = 0;
$quantidadeTotalCarrinho = 0;

foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $id = (int)$id;
    $quantidade = (int)$quantidade;

    if ($id < 1 || $quantidade < 1) {
        continue;
    }

    $sqlCarrinho = "
        SELECT id, nome, preco, preco_promocional, estoque, imagem, ativo
        FROM produtos
        WHERE id = ?
        LIMIT 1
    ";

    $stmtCarrinho = $conexao->prepare($sqlCarrinho);
    $stmtCarrinho->bind_param("i", $id);
    $stmtCarrinho->execute();
    $produtoCarrinho = $stmtCarrinho->get_result()->fetch_assoc();
    $stmtCarrinho->close();

    if (!$produtoCarrinho) {
        continue;
    }

    $precoCarrinho = (float)$produtoCarrinho['preco'];

    if (
        $produtoCarrinho['preco_promocional'] !== null &&
        (float)$produtoCarrinho['preco_promocional'] > 0 &&
        (float)$produtoCarrinho['preco_promocional'] < $precoCarrinho
    ) {
        $precoCarrinho = (float)$produtoCarrinho['preco_promocional'];
    }

    $precoUnidadeCentavos = (int)round($precoCarrinho * 100);
    $subtotalCentavos = $precoUnidadeCentavos * $quantidade;

    $produtoCarrinho['quantidade_carrinho'] = $quantidade;
    $produtoCarrinho['preco_carrinho'] = $precoCarrinho;
    $produtoCarrinho['subtotal_centavos'] = $subtotalCentavos;

    $itensCarrinho[] = $produtoCarrinho;

    $totalCarrinhoCentavos += $subtotalCentavos;
    $quantidadeTotalCarrinho += $quantidade;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chingling - Móveis para todos os momentos</title>

    <link rel="stylesheet" href="style2.css?v=7">
</head>

<body>

    <div id="topo-beneficios">

        <div class="topo-esquerda">
            <span>🚚 Entrega para todo o Brasil</span>
            <span class="separador-topo">|</span>
            <span>✔️ Compra segura</span>
            <span class="separador-topo">|</span>
            <span>⭐ Qualidade e conforto para o seu lar</span>
        </div>

        <div class="topo-direita">

            <div class="usuario-topo">

                <?php if (!empty($usuarioAtual['imagem'])): ?>

                    <img
                        src="<?= escapar($usuarioAtual['imagem']) ?>"
                        alt="Foto de perfil"
                        class="foto-perfil-topo"
                    >

                <?php else: ?>

                    <span class="foto-perfil-padrao">👤</span>

                <?php endif; ?>

                <span>Olá, <?= escapar($usuarioAtual['nome']) ?>!</span>

            </div>

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

                <p class="banner-descricao">
                    Móveis modernos, funcionais e com ótimos preços.
                    Encontre as peças ideais para transformar sua casa
                    em um ambiente ainda mais especial.
                </p>

                <a href="#secao-produtos" class="btn-oferta">
                    Ver móveis disponíveis →
                </a>

            </div>

            <div class="banner-cards">
                <div class="card-selo">🚚 Entrega para todo o Brasil</div>
                <div class="card-selo">🔒 Compra segura e protegida</div>
                <div class="card-selo">🛡️ Qualidade e conforto para seu lar</div>
            </div>

        </section>

            <!-- PAINEL LATERAL DE SALDO -->
            <aside class="saldo-caixa">

                <h2>Meu saldo</h2>

                <p class="saldo-valor">
                    R$ <?= number_format((float)$usuarioAtual['saldo'], 2, ',', '.') ?>
                </p>

                <form method="POST" action="Painel.php">

                    <input type="hidden" name="csrf_token" value="<?= escapar($_SESSION['csrf_painel']) ?>">
                    <input type="hidden" name="acao" value="recarga">

                    <label for="valor_recarga">Recarregar saldo</label>

                    <select name="valor_recarga" id="valor_recarga" required>
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

                <p class="aviso-recarga">Recarga simulada para testes.</p>

            </div>

        <?php if ($mensagemPainel !== ''): ?>
            <div class="mensagem-painel" role="status">
                <?= escapar($mensagemPainel) ?>
            </div>
        <?php endif; ?>

        <section class="secao-produtos" id="secao-produtos">

            <div class="titulo-secao-produtos">
                <div>
                    <span class="subtitulo-secao">ENCONTRE O QUE PROCURA</span>
                    <h2>Móveis disponíveis</h2>
                    <p>Confira nossos móveis e encontre o ideal para sua casa.</p>
                </div>
            </div>

            <!-- PESQUISA POR NOME, CATEGORIA E FAIXA DE PREÇO -->
            <form method="GET" action="Painel.php" class="pesquisa-painel">

                <div class="campo-pesquisa-painel">
                    <label for="busca_nome">Nome do produto</label>
                    <input
                        type="text"
                        id="busca_nome"
                        name="busca_nome"
                        placeholder="Ex.: Mesa, cadeira..."
                        value="<?= escapar($buscaNome) ?>"
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
                                <?= escapar($cat['nome']) ?>
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
                        value="<?= escapar($precoMinTexto) ?>"
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
                        value="<?= escapar($precoMaxTexto) ?>"
                    >
                </div>

                <button type="submit" class="botao-pesquisar-painel">
                    Pesquisar
                </button>

                <a href="Painel.php" class="botao-limpar-pesquisa">
                    Limpar filtros
                </a>

</form>

            <?php if (count($produtos) === 0): ?>

                <p class="sem-produtos">
                    Nenhum móvel encontrado com os filtros selecionados.
                </p>

            <?php else: ?>

                <div class="lista-produtos-painel">

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
                        $imagemProduto = caminhoImagem($produto['imagem'] ?? '');
                        $quantidadeNoCarrinho = (int)($_SESSION['carrinho'][$produto['id']] ?? 0);
                        ?>

                        <article class="produto-painel-card">

                            <div class="produto-card-imagem-area">

                                <?php if ($imagemProduto !== ''): ?>

                                    <img
                                        src="<?= escapar($imagemProduto) ?>"
                                        alt="<?= escapar($produto['nome']) ?>"
                                        class="produto-painel-imagem"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >

                                    <div class="sem-imagem" style="display:none;">
                                        Imagem não disponível
                                    </div>

                                <?php else: ?>

                                    <div class="sem-imagem">
                                        Imagem não disponível
                                    </div>

                                <?php endif; ?>

                            </div>

                            <div class="produto-card-conteudo">

                                <span class="produto-categoria">
                                    <?= escapar($produto['categoria'] ?? 'Não definida') ?>
                                </span>

                                <h3><?= escapar($produto['nome']) ?></h3>

                                <div class="produto-painel-dados">

                                    <p>
                                        <strong>Estoque:</strong>
                                        <?= (int)$produto['estoque'] ?> unidade(s)
                                    </p>

                                    <p class="produto-descricao">
                                        <?= escapar($produto['descricao'] ?: 'Sem descrição.') ?>
                                    </p>

                                </div>

                                <div class="produto-card-rodape">

                                    <div class="produto-preco">

                                        <?php if ($precoFinal < (float)$produto['preco']): ?>
                                            <span class="preco-antigo">
                                                R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?>
                                            </span>
                                        <?php endif; ?>

                                        <strong>
                                            R$ <?= number_format($precoFinal, 2, ',', '.') ?>
                                        </strong>

                                    </div>

                                    <?php if ($semEstoque): ?>

                                        <button type="button" class="botao-comprar" disabled>
                                            Sem estoque
                                        </button>

                                    <?php else: ?>

                                        <form method="POST" action="Painel.php" class="form-comprar">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= escapar($_SESSION['csrf_painel']) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="acao"
                                                value="adicionar_carrinho"
                                            >

                                            <input
                                                type="hidden"
                                                name="produto_id"
                                                value="<?= (int)$produto['id'] ?>"
                                            >

                                            <button type="submit" class="botao-comprar">
                                                🛒 Adicionar ao carrinho
                                            </button>

                                        </form>

                                        <?php if ($quantidadeNoCarrinho > 0): ?>
                                            <small class="produto-no-carrinho">
                                                <?= $quantidadeNoCarrinho ?> unidade(s) no carrinho
                                            </small>
                                        <?php endif; ?>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
