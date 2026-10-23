
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

    $sql = "SELECT id, nome, estoque, preco, categoria_id, imagem, descricao
            FROM produtos
            WHERE nome LIKE ?";

    $nomeLike = '%' . $nomeBusca . '%';

    if ($categoriaBusca !== '') {
        $sql .= " AND categoria_id = ?";
    }

    $sql .= " ORDER BY nome ASC";

    $stmt = $conexao->prepare($sql);

    if ($stmt) {

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

    } else {
        $erroPesquisa = 'Não foi possível preparar a pesquisa.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Administração de Móveis</title>

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
        <a href="AdmCadastroMoveis.php">Cadastro de Móveis (ADM ONLY)</a>
    </nav>

    <main>

        <h1>ADMINISTRAÇÃO DE MÓVEIS</h1>

        <p class="descricao">
            Pesquise móveis para acessar suas informações, editar ou excluir.
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

            <form method="GET" action="AdmMoveis.php">

                <label for="nome_busca">Nome do móvel</label>

                <input
                    type="text"
                    name="nome_busca"
                    id="nome_busca"
                    placeholder="Digite o nome do móvel"
                    value="<?php
                        echo htmlspecialchars(
                            $nomeBusca,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <br><br>

                <label for="categoria_busca">Categoria</label>

                <select name="categoria_busca" id="categoria_busca">

                    <option value="">Todas as categorias</option>

                    <option
                        value="1"
                        <?php echo $categoriaBusca === '1' ? 'selected' : ''; ?>
                    >
                        Cozinha
                    </option>

                    <option
                        value="2"
                        <?php echo $categoriaBusca === '2' ? 'selected' : ''; ?>
                    >
                        Sala de Estar
                    </option>

                    <option
                        value="3"
                        <?php echo $categoriaBusca === '3' ? 'selected' : ''; ?>
                    >
                        Quarto
                    </option>

                    <option
                        value="4"
                        <?php echo $categoriaBusca === '4' ? 'selected' : ''; ?>
                    >
                        Diversos
                    </option>

                </select>

                <br><br>

                <button
                    type="submit"
                    name="pesquisar"
                    value="1"
                    class="botao-alterar"
                >
                    PESQUISAR
                </button>

            </form>

            <!-- RESULTADOS DA PESQUISA -->

            <?php if (isset($_GET['pesquisar'])): ?>

                <div class="resultados-pesquisa">

                    <h2>Resultados da pesquisa</h2>

                    <?php if ($erroPesquisa !== ''): ?>

                        <p>
                            <?php
                            echo htmlspecialchars(
                                $erroPesquisa,
                                ENT_QUOTES,
                                'UTF-8'
                            );
                            ?>
                        </p>

                    <?php elseif (
                        $resultadoPesquisa &&
                        $resultadoPesquisa->num_rows > 0
                    ): ?>

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

                        <?php while (
                            $movel = $resultadoPesquisa->fetch_assoc()
                        ): ?>

                            <div class="produto-resultado">

                                <h3>
                                    ID: <?php echo (int) $movel['id']; ?>
                                    -
                                    <?php
                                    echo htmlspecialchars(
                                        $movel['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </h3>

                                <p>
                                    <strong>Categoria:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $categorias[
                                            (int) $movel['categoria_id']
                                        ] ?? 'Não identificada',
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
                                    R$
                                    <?php
                                    echo number_format(
                                        (float) $movel['preco'],
                                        2,
                                        ',',
                                        '.'
                                    );
                                    ?>
                                </p>

                                <p>
                                    <strong>Descrição:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $movel['descricao'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </p>

                                <!-- BOTÃO DE EXCLUIR -->

                                <form
                                    action="AdmMoveisDelete_back.php"
                                    method="POST"
                                    onsubmit="return confirm('Tem certeza de que deseja excluir este móvel?');"
                                >

                                    <input
                                        type="hidden"
                                        name="delete_id"
                                        value="<?php echo (int) $movel['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="fdeletar"
                                        value="1"
                                        class="botao-alterar"
                                    >
                                        EXCLUIR
                                    </button>

                                </form>

                                <br>

                                <!-- BOTÃO DE EDITAR -->

                                <button
                                    type="button"
                                    class="botao-alterar"
                                    onclick="revelarFormulario(this)"
                                    data-movel="<?php
                                        echo htmlspecialchars(
                                            json_encode([
                                                'id' => $movel['id'],
                                                'nome' => $movel['nome'],
                                                'estoque' => $movel['estoque'],
                                                'preco' => $movel['preco'],
                                                'categoria' => $movel['categoria_id'],
                                                'imagem' => $movel['imagem'] ?? '',
                                                'descricao' => $movel['descricao'] ?? ''
                                            ]),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                    ?>"
                                >
                                    EDITAR
                                </button>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <p>
                            Nenhum móvel encontrado com esses filtros.
                        </p>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

        <!-- FIM DA PESQUISA -->

        <!-- FORMULÁRIO DE EDIÇÃO -->

        <div
            class="formularios"
            id="formulario_edicao"
            style="display: none;"
        >

            <div class="caixa">

                <h2>Edição de móveis</h2>

                <p class="descricao">
                    Altere as informações do móvel selecionado.
                </p>

                <form
                    method="POST"
                    action="AdmMoveisEdit_back.php"
                >

                    <!-- ID DO MÓVEL QUE SERÁ EDITADO -->

                    <input
                        type="hidden"
                        name="movel_id"
                        id="edit_movel_id"
                    >

                    <label for="edit_movel_nome">
                        Nome do móvel
                    </label>

                    <input
                        type="text"
                        name="movel_nome"
                        id="edit_movel_nome"
                        required
                    >

                    <br><br>

                    <label for="edit_movel_estoque">
                        Estoque do móvel
                    </label>

                    <input
                        type="number"
                        name="movel_estoque"
                        id="edit_movel_estoque"
                        min="0"
                        required
                    >

                    <br><br>

                    <label for="edit_movel_preco">
                        Preço do móvel
                    </label>

                    <input
                        type="number"
                        name="movel_preco"
                        id="edit_movel_preco"
                        step="0.01"
                        min="0"
                        required
                    >

                    <br><br>

                    <label for="edit_categoria">
                        Categoria do móvel
                    </label>

                    <select
                        name="categoria"
                        id="edit_categoria"
                        required
                    >
                        <option value="1">Cozinha</option>
                        <option value="2">Sala de Estar</option>
                        <option value="3">Quarto</option>
                        <option value="4">Diversos</option>
                    </select>

                    <br><br>

                    <label for="edit_movel_imagem">
                        Imagem do móvel
                    </label>

                    <input
                        type="text"
                        name="movel_imagem"
                        id="edit_movel_imagem"
                        required
                    >

                    <br><br>

                    <label for="edit_movel_descricao">
                        Descrição do móvel
                    </label>

                    <input
                        type="text"
                        name="movel_descricao"
                        id="edit_movel_descricao"
                        required
                    >

                    <br><br>

                    <button
                        type="submit"
                        class="botao-alterar"
                    >
                        SALVAR ALTERAÇÕES
                    </button>

                    <button
                        type="button"
                        class="botao-alterar"
                        onclick="fecharFormulario()"
                    >
                        CANCELAR
                    </button>

                </form>

            </div>

        </div>

        <!-- FIM DO FORMULÁRIO DE EDIÇÃO -->

    </main>

    <script>
        function revelarFormulario(botao) {

            // Recupera os dados do móvel selecionado.
            const movel = JSON.parse(botao.dataset.movel);

            // Preenche os campos do formulário.
            document.getElementById("edit_movel_id").value = movel.id;
            document.getElementById("edit_movel_nome").value = movel.nome;
            document.getElementById("edit_movel_estoque").value = movel.estoque;
            document.getElementById("edit_movel_preco").value = movel.preco;
            document.getElementById("edit_categoria").value = movel.categoria;
            document.getElementById("edit_movel_imagem").value = movel.imagem ?? "";
            document.getElementById("edit_movel_descricao").value = movel.descricao ?? "";

            // Mostra o formulário.
            const formulario = document.getElementById("formulario_edicao");

            formulario.style.display = "block";

            // Desce a página até o formulário.
            formulario.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }

        function fecharFormulario() {

            document.getElementById("formulario_edicao").style.display = "none";

        }
    </script>

</body>

</html>

