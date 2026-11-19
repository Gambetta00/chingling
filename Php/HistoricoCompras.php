<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
require_once "conexao_msql.php";

if (empty($_SESSION['email'])) {
    header("Location: Login.php");
    exit;
}

$emailUsuario = $_SESSION['email'];

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function dinheiro($valor): string {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

$stmt = $conexao->prepare(
    "SELECT nome, saldo, tipo, imagem FROM usuarios WHERE email = ?"
);
$stmt->bind_param("s", $emailUsuario);
$stmt->execute();
$usuarioAtual = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$usuarioAtual) {
    session_destroy();
    header("Location: Login.php");
    exit;
}

$compras = [];
$erroHistorico = '';

try {

    $sql = "
        SELECT
            co.id,
            co.pedido_codigo,
            co.data_compra,
            co.produto_id,
            co.preco_pago,
            co.quantidade,
            p.nome        AS produto_nome,
            p.descricao   AS produto_descricao,
            p.imagem      AS produto_imagem,
            c.nome        AS categoria_nome
        FROM compras co
        LEFT JOIN produtos   p ON p.id = co.produto_id
        LEFT JOIN categorias c ON c.id = co.categoria_id
        WHERE co.usuario_email = ?
        ORDER BY co.data_compra DESC, co.pedido_codigo DESC, co.id ASC
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $emailUsuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($linha = $resultado->fetch_assoc()) {

        $temCodigo = !empty($linha['pedido_codigo']);
        $chave = $temCodigo ? $linha['pedido_codigo'] : 'antigo-' . $linha['id'];

        if (!isset($compras[$chave])) {
            $compras[$chave] = [
                'rotulo'         => $temCodigo
                                    ? strtoupper(substr($linha['pedido_codigo'], 0, 8))
                                    : 'ANT-' . $linha['id'],
                'carrinho'       => $temCodigo,
                'data'           => $linha['data_compra'],
                'itens'          => [],
                'quantidade'     => 0,
                'total_centavos' => 0,
            ];
        }

        $quantidade = (int)$linha['quantidade'];
        $subtotalCentavos = (int)round((float)$linha['preco_pago'] * 100) * $quantidade;

        $linha['subtotal_centavos'] = $subtotalCentavos;

        $compras[$chave]['itens'][] = $linha;
        $compras[$chave]['quantidade'] += $quantidade;
        $compras[$chave]['total_centavos'] += $subtotalCentavos;
    }

    $stmt->close();

} catch (mysqli_sql_exception $e) {

    $erroHistorico = "Não foi possível carregar o histórico. "
                   . "Verifique se as colunas pedido_codigo e data_compra "
                   . "existem na tabela compras.";
}

$totalCompras = count($compras);
$totalProdutos = 0;
$totalGastoCentavos = 0;

foreach ($compras as $compra) {
    $totalProdutos += $compra['quantidade'];
    $totalGastoCentavos += $compra['total_centavos'];
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Histórico de Compras - Chingling</title>

    <link rel="stylesheet" href="style2.css?v=8">
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

            <form action="Login.php" method="POST">
                <input type="hidden" name="sinal_clique" value="ativado">
                <button type="submit" class="botao-sair">Sair da conta</button>
            </form>

            <a href="UserAlterPerfil.php" class="botao-sair">
                Editar perfil
            </a>

        </div>

    </div>

    <div id="barra-principal">

        <a href="Painel.php" class="logo">
            <span>🏠 Chingling</span>
            <small>Móveis para todos os momentos</small>
        </a>

        <form method="GET" action="Painel.php" class="busca busca-painel-topo">

            <input
                type="text"
                name="busca_nome"
                placeholder="Olá, o que você procura hoje?"
                aria-label="Pesquisar móveis"
            >

            <button type="submit" aria-label="Pesquisar">🔍</button>

        </form>

        <div class="atalho-saldo-topo">
            <span>💳</span>

            <div>
                <strong>Meu saldo</strong>
                <small><?= dinheiro($usuarioAtual['saldo']) ?></small>
            </div>
        </div>

    </div>

    <nav id="menu-categorias">

        <a href="Painel.php">Início</a>
        <a href="HistoricoCompras.php">Histórico de compras</a>

        <?php if ($usuarioAtual['tipo'] === 'admin'): ?>
            <a href="AdmMoveis.php">Administração de Móveis</a>
            <a href="AdmUsuarios.php">Administração de Usuários</a>
        <?php endif; ?>

    </nav>

    <main class="historico-container">

        <section class="historico-introducao">

            <span class="subtitulo-secao">MINHAS COMPRAS</span>

            <h1>Histórico de compras</h1>

            <p>
                Acompanhe tudo o que você já comprou na Chingling.
                Cada compra feita pelo carrinho aparece junto, com os
                detalhes de todos os móveis.
            </p>

        </section>

        <?php if ($erroHistorico !== ''): ?>

            <div class="historico-erro" role="alert">
                <?= escapar($erroHistorico) ?>
            </div>

        <?php elseif ($totalCompras === 0): ?>

            <div class="historico-vazio">
                <span>🛍️</span>
                <h2>Você ainda não fez nenhuma compra</h2>
                <p>Quando você finalizar uma compra, ela aparecerá aqui.</p>
                <a href="Painel.php" class="historico-botao-voltar">
                    Ver móveis disponíveis →
                </a>
            </div>

        <?php else: ?>

            <section class="historico-resumo">

                <div class="historico-resumo-card">
                    <span class="historico-resumo-icone">🧾</span>
                    <div>
                        <small>Compras realizadas</small>
                        <strong><?= $totalCompras ?></strong>
                    </div>
                </div>

                <div class="historico-resumo-card">
                    <span class="historico-resumo-icone">🪑</span>
                    <div>
                        <small>Móveis comprados</small>
                        <strong><?= $totalProdutos ?></strong>
                    </div>
                </div>

                <div class="historico-resumo-card">
                    <span class="historico-resumo-icone">💰</span>
                    <div>
                        <small>Total gasto</small>
                        <strong><?= dinheiro($totalGastoCentavos / 100) ?></strong>
                    </div>
                </div>

            </section>

            <?php foreach ($compras as $compra): ?>

                <?php
                $qtdItensDiferentes = count($compra['itens']);
                $dataFormatada = $compra['data']
                    ? date('d/m/Y \à\s H:i', strtotime($compra['data']))
                    : 'Data não registrada';
                ?>

                <article class="historico-compra">

                    <header class="historico-compra-cabecalho">

                        <div class="historico-compra-titulo">

                            <h2>Compra #<?= escapar($compra['rotulo']) ?></h2>

                            <span class="historico-compra-data">
                                📅 <?= escapar($dataFormatada) ?>
                            </span>

                        </div>

                        <div class="historico-compra-selos">

                            <?php if ($compra['carrinho']): ?>
                                <span class="historico-selo">
                                    🛒 Compra pelo carrinho
                                </span>
                            <?php else: ?>
                                <span class="historico-selo historico-selo-antigo">
                                    Compra anterior
                                </span>
                            <?php endif; ?>

                            <span class="historico-selo historico-selo-claro">
                                <?= $compra['quantidade'] ?>
                                <?= $compra['quantidade'] === 1 ? 'produto' : 'produtos' ?>
                            </span>

                        </div>

                    </header>

                    <div class="historico-itens">

                        <?php foreach ($compra['itens'] as $item): ?>

                            <?php
                            $nomeProduto = $item['produto_nome'] ?? null;
                            $produtoRemovido = ($nomeProduto === null);
                            $imagemItem = trim((string)($item['produto_imagem'] ?? ''));
                            ?>

                            <div class="historico-item">

                                <div class="historico-item-imagem-area">

                                    <?php if ($imagemItem !== ''): ?>

                                        <img
                                            src="<?= escapar($imagemItem) ?>"
                                            alt="<?= escapar($nomeProduto ?? 'Produto') ?>"
                                            class="historico-item-imagem"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >

                                        <div class="historico-item-sem-imagem" style="display:none;">
                                            Sem imagem
                                        </div>

                                    <?php else: ?>

                                        <div class="historico-item-sem-imagem">
                                            Sem imagem
                                        </div>

                                    <?php endif; ?>

                                </div>

                                <div class="historico-item-info">

                                    <?php if (!empty($item['categoria_nome'])): ?>
                                        <span class="produto-categoria">
                                            <?= escapar($item['categoria_nome']) ?>
                                        </span>
                                    <?php endif; ?>

                                    <h3>
                                        <?= $produtoRemovido
                                            ? 'Produto removido da loja'
                                            : escapar($nomeProduto) ?>
                                    </h3>

                                    <p class="historico-item-descricao">
                                        <?= $produtoRemovido
                                            ? 'Este móvel não está mais disponível no catálogo.'
                                            : escapar($item['produto_descricao'] ?: 'Sem descrição.') ?>
                                    </p>

                                </div>

                                <div class="historico-item-valores">

                                    <div>
                                        <small>Preço pago (un.)</small>
                                        <strong><?= dinheiro($item['preco_pago']) ?></strong>
                                    </div>

                                    <div>
                                        <small>Quantidade</small>
                                        <strong><?= (int)$item['quantidade'] ?></strong>
                                    </div>

                                    <div>
                                        <small>Subtotal</small>
                                        <strong class="historico-subtotal">
                                            <?= dinheiro($item['subtotal_centavos'] / 100) ?>
                                        </strong>
                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <footer class="historico-compra-rodape">

                        <span>
                            <?= $qtdItensDiferentes ?>
                            <?= $qtdItensDiferentes === 1 ? 'item' : 'itens' ?>
                            nesta compra
                        </span>

                        <span class="historico-compra-total">
                            Total da compra:
                            <strong><?= dinheiro($compra['total_centavos'] / 100) ?></strong>
                        </span>

                    </footer>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

        <div class="perfil-rodape">
            <a href="Painel.php">← Voltar para o início</a>
        </div>

    </main>

</body>
</html>
