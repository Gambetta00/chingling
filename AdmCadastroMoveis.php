<?php
session_start();

require_once 'conexao_msql.php';

$nomeBusca = '';
$categoriaBusca = '';
$resultadoPesquisa = null;
$erroPesquisa = '';

if (isset($_GET['pesquisar'])) {

    $nomeBusca = trim($_GET['nome_busca'] ?? '');
    $categoriaBusca = $_GET['categoria_busca'] ?? '';

    $sql = "SELECT id, nome, estoque, preco, categoria_id, descricao
            FROM produtos
            WHERE nome LIKE ?";

    $nomeLike = '%' . $nomeBusca . '%';

    if ($categoriaBusca !== '') {
        $sql .= " AND categoria_id = ?";
    }

    $sql .= " ORDER BY nome ASC";

    $stmt = $conexao->prepare($sql);

    if ($categoriaBusca !== '') {
        $categoriaId = (int) $categoriaBusca;
        $stmt->bind_param("si", $nomeLike, $categoriaId);
    } else {
        $stmt->bind_param("s", $nomeLike);
    }

    if ($stmt->execute()) {
        $resultadoPesquisa = $stmt->get_result();
    } else {
        $erroPesquisa = 'Não foi possível pesquisar os móveis.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Móveis</title>

    <link rel="stylesheet" href="style2.css">
</head>

<body>

    <header>

        <div class="logo">
            <span>🏠</span>
            <h1>Chingling</h1>
        </div>

        <div class="usuario">
            <form action="Login.php" method="POST">
                <input
                    type="hidden"
                    name="sinal_clique"
                    value="ativado"
                >

                <button type="submit" class="logout">
                    Sair da conta
                </button>
            </form>
        </div>

    </header>

    <nav>
        <a href="Painel.php">Início</a>
        <a href="Moveis.php">Móveis</a>
        <a href="AdmMoveis.php">Administração de Móveis (ADM ONLY)</a>
        <a href="AdmUsuarios.php">Administração de Usuários (ADM ONLY)</a>
    </nav>

    <main>

        <h1>ADMINISTRAÇÃO DE MÓVEIS</h1>

        <p class="descricao">
            Cadastre novos móveis ou edite móveis já existentes na loja.
        </p>

        <?php

        if (isset($_SESSION['mensagem'])) {
            echo '<p class="mensagem">' .
                htmlspecialchars($_SESSION['mensagem'], ENT_QUOTES, 'UTF-8') .
                '</p>';

            unset($_SESSION['mensagem']);
        }
        ?>

        <!-- PESQUISA DE MÓVEIS -->

<div class="caixa pesquisa-caixa">

    <h2>Pesquisar móveis</h2>

    <p class="descricao">
        Pesquise pelo nome do móvel ou selecione uma categoria.
    </p>


    <form method="GET" action="">

        <label for="nome_busca">Nome do móvel</label>

        <input
            type="text"
            name="nome_busca"
            id="nome_busca"
            placeholder="Digite o nome do móvel"
            value="<?php echo htmlspecialchars($nomeBusca, ENT_QUOTES, 'UTF-8'); ?>"
        >

        <br><br>

        <label for="categoria_busca">Categoria</label>

        <select name="categoria_busca" id="categoria_busca">

            <option value="">Todas as categorias</option>

            <option value="1" <?php echo $categoriaBusca === '1' ? 'selected' : ''; ?>>
                Cozinha
            </option>

            <option value="2" <?php echo $categoriaBusca === '2' ? 'selected' : ''; ?>>
                Sala de Estar
            </option>

            <option value="3" <?php echo $categoriaBusca === '3' ? 'selected' : ''; ?>>
                Quarto
            </option>

            <option value="4" <?php echo $categoriaBusca === '4' ? 'selected' : ''; ?>>
                Diversos
            </option>

        </select>

        <br><br>

        <button type="submit" name="pesquisar" value="1" class="botao-alterar">
            PESQUISAR
        </button>

    </form>

    <?php if (isset($_GET['pesquisar'])): ?>

        <div class="resultados-pesquisa">

            <h2>Resultados da pesquisa</h2>

            <?php if ($erroPesquisa !== ''): ?>

                <p><?php echo htmlspecialchars($erroPesquisa, ENT_QUOTES, 'UTF-8'); ?></p>

            <?php elseif ($resultadoPesquisa && $resultadoPesquisa->num_rows > 0): ?>

                <p>
                    Foram encontrados
                    <?php echo $resultadoPesquisa->num_rows; ?>
                    móvel(is).
                </p>

                <?php
                $categorias = [
                    1 => 'Cozinha',
                    2 => 'Sala de Estar',
                    3 => 'Quarto',
                    4 => 'Diversos'
                ];
                ?>

                <?php while ($movel = $resultadoPesquisa->fetch_assoc()): ?>

                    <div class="produto-resultado">
                        
                        <h3>
                        ID: <?php echo (int) $movel['id']; ?> -
                        <?php echo htmlspecialchars($movel['nome'], ENT_QUOTES, 'UTF-8'); ?>
                        </h3>

                        <p>
                            <strong>Categoria:</strong>
                            <?php
                            echo htmlspecialchars(
                                $categorias[(int) $movel['categoria_id']] ?? 'Não identificada',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </p>

                        <p>
                            <strong>Estoque:</strong>
                            <?php echo (int) $movel['estoque']; ?>
                        </p>

                        <p>
                            <strong>Preço:</strong>
                            R$ <?php echo number_format((float) $movel['preco'], 2, ',', '.'); ?>
                        </p>

                        <p>
                            <strong>Descrição:</strong>
                            <?php echo htmlspecialchars($movel['descricao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </p>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p>Nenhum móvel encontrado com esses filtros.</p>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

<!-- FIM DA PESQUISA -->

        <div class="formularios">

            <!-- FORMULÁRIO DE CADASTRO -->

            <div class="caixa">

                <h2>Cadastro de móveis</h2>

                <p class="descricao">
                    Cadastre um novo móvel. Não é permitido repetir o nome
                    e a categoria de um móvel existente.
                </p>

                
            </div>

                <form method="POST" action="AdmMoveis_back.php">

                    <label for="cad_movel_nome">Nome do móvel</label>
                    <input
                        type="text"
                        name="movel_nome"
                        id="cad_movel_nome"
                        required
                    >

                    <br><br>

                    <label for="cad_movel_estoque">Estoque do móvel</label>
                    <input
                        type="number"
                        name="movel_estoque"
                        id="cad_movel_estoque"
                        min="0"
                        required
                    >

                    <br><br>

                    <label for="cad_movel_preco">Preço do móvel</label>
                    <input
                        type="number"
                        name="movel_preco"
                        id="cad_movel_preco"
                        step="0.01"
                        min="0"
                        required
                    >

                    <br><br>

                    <label for="cad_categoria">Categoria do móvel</label>
                    <select name="categoria" id="cad_categoria" required>
                        <option value="1">Cozinha</option>
                        <option value="2">Sala de Estar</option>
                        <option value="3">Quarto</option>
                        <option value="4">Diversos</option>
                    </select>

                    <br><br>

                    <label for="cad_movel_imagem">Imagem do móvel</label>
                    <input
                        type="text"
                        name="movel_imagem"
                        id="cad_movel_imagem"
                        required
                    >

                    <br><br>

                    <label for="cad_movel_descricao">Descrição do móvel</label>
                    <input
                        type="text"
                        name="movel_descricao"
                        id="cad_movel_descricao"
                        required
                    >

                    <br><br>

                    <button type="submit" class="botao-alterar">
                        CADASTRAR
                    </button>

                </form>

            </div>

            <!-- FORMULÁRIO DE EDIÇÃO -->

            <div class="caixa">

                <h2>Edição de móveis</h2>

                <p class="descricao">
                    Informe o nome e a categoria do móvel que deseja localizar.
                    Os demais campos serão usados para atualizar seus dados.
                </p>

                <form method="POST" action="AdmMoveisEdit_back.php">

                    <label for="edit_movel_nome">Nome do móvel</label>
                    <input
                        type="text"
                        name="movel_nome"
                        id="edit_movel_nome"
                        required
                    >

                    <br><br>

                    <label for="edit_movel_estoque">Estoque do móvel</label>
                    <input
                        type="number"
                        name="movel_estoque"
                        id="edit_movel_estoque"
                        min="0"
                        required
                    >

                    <br><br>

                    <label for="edit_movel_preco">Preço do móvel</label>
                    <input
                        type="number"
                        name="movel_preco"
                        id="edit_movel_preco"
                        step="0.01"
                        min="0"
                        required
                    >

                    <br><br>

                    <label for="edit_categoria">Categoria do móvel</label>
                    <select name="categoria" id="edit_categoria" required>
                        <option value="1">Cozinha</option>
                        <option value="2">Sala de Estar</option>
                        <option value="3">Quarto</option>
                        <option value="4">Diversos</option>
                    </select>

                    <br><br>

                    <label for="edit_movel_imagem">Imagem do móvel</label>
                    <input
                        type="text"
                        name="movel_imagem"
                        id="edit_movel_imagem"
                        required
                    >

                    <br><br>

                    <label for="edit_movel_descricao">Descrição do móvel</label>
                    <input
                        type="text"
                        name="movel_descricao"
                        id="edit_movel_descricao"
                        required
                    >

                    <br><br>

                    <button type="submit" class="botao-alterar">
                        EDITAR MÓVEL
                    </button>

                </form>

            </div>

            <div class="caixa">

                <h2>Apagar móveis</h2>

                <p class="descricao">
                    Informe o nome e a categoria do móvel que deseja localizar.
                    Os demais campos serão usados para excluir um móvel.
                    AÇÕES AQUI NÃO PODEM SER DESFEITAS!
                </p>

                <form method="POST" action="AdmMoveisDelete_back.php">

                    <label for="delete_movel_nome">Nome do móvel</label>
                    <input
                        type="text"
                        name="movel_nome"
                        id="delete_movel_nome"
                        required
                    >

                    <br><br>

                    <label for="edit_categoria">Categoria do móvel</label>
                    <select name="categoria" id="edit_categoria" required>
                        <option value="1">Cozinha</option>
                        <option value="2">Sala de Estar</option>
                        <option value="3">Quarto</option>
                        <option value="4">Diversos</option>
                    </select>

                    <br><br>

                    <button type="submit" class="botao-alterar">
                        Deletar Móvel 
                    </button>

                </form>

            </div>

        </div>

    </main>

</body>

</html>