
<?php
session_start();

require_once 'conexao_msql.php';

// Verifica se o usuário está logado.
if (empty($_SESSION['email'])) {
    header("Location: Login.php");
    exit;
}

// Verifica se o usuário é administrador.
$sqlUsuario = "SELECT nome, tipo FROM usuarios WHERE email = ?";
$stmtUsuario = $conexao->prepare($sqlUsuario);
$stmtUsuario->bind_param("s", $_SESSION['email']);
$stmtUsuario->execute();

$usuarioAtual = $stmtUsuario->get_result()->fetch_assoc();
$stmtUsuario->close();

if (!$usuarioAtual || $usuarioAtual['tipo'] !== 'admin') {
    header("Location: Painel.php");
    exit;
}

$_SESSION['tipo'] = 'admin';

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Móveis - Chingling</title>

    <link rel="stylesheet" href="style2.css?v=8">

</head>

<body>

    <!-- BARRA SUPERIOR -->

    <div id="topo-beneficios">

        <div class="topo-esquerda">

            <span>🚚 Entrega para todo o Brasil</span>
            <span class="separador-topo">|</span>
            <span>✔️ Compra segura</span>
            <span class="separador-topo">|</span>
            <span>⭐ Qualidade e conforto para o seu lar</span>

        </div>

        <div class="topo-direita">

            <span>Olá, <?= escapar($usuarioAtual['nome']) ?>!</span>

            <form action="Login.php" method="POST">

                <input
                    type="hidden"
                    name="sinal_clique"
                    value="ativado"
                >

                <button type="submit" class="botao-sair">
                    Sair da conta
                </button>

            </form>

        </div>

    </div>

    <!-- BARRA PRINCIPAL -->

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

            <button type="submit" aria-label="Pesquisar">
                🔍
            </button>

        </form>

        <div class="atalho-saldo-topo">

            <span>⚙️</span>

            <div>
                <strong>Área administrativa</strong>
                <small>Cadastro de produtos</small>
            </div>

        </div>

    </div>

    <!-- MENU -->

    <nav id="menu-categorias">

        <a href="Painel.php">Início</a>

        <a href="Moveis.php">Móveis</a>

        <a href="AdmMoveis.php">Administração de Móveis</a>

        <a href="AdmUsuarios.php">Administração de Usuários</a>

        <a href="AdmCadastroMoveis.php">Cadastro de Móveis</a>

    </nav>

    <!-- CONTEÚDO PRINCIPAL -->

    <main class="adm-cadastro-container">

        <section class="adm-cadastro-introducao">

            <span class="adm-cadastro-etiqueta">
                ÁREA ADMINISTRATIVA
            </span>

            <h1>Cadastro de Móveis</h1>

            <p>
                Adicione novos produtos ao catálogo da Chingling.
                Preencha as informações e escolha a imagem que será
                exibida para os clientes.
            </p>

        </section>

        <?php if (!empty($mensagem)): ?>

            <div class="adm-cadastro-mensagem">
                <?= escapar($mensagem) ?>
            </div>

        <?php endif; ?>

        <section class="adm-cadastro-caixa">

            <div class="adm-cadastro-cabecalho">

                <span class="adm-cadastro-icone">🪑</span>

                <div>

                    <h2>Informações do móvel</h2>

                    <p>
                        Preencha os dados para cadastrar um novo produto.
                    </p>

                </div>

            </div>

            <form
                method="POST"
                action="AdmMoveis_back.php"
                enctype="multipart/form-data"
                class="adm-cadastro-formulario"
            >

                <div class="adm-cadastro-campos">

                    <!-- NOME -->

                    <div class="adm-cadastro-campo">

                        <label for="cad_movel_nome">
                            Nome do móvel
                        </label>

                        <input
                            type="text"
                            name="movel_nome"
                            id="cad_movel_nome"
                            placeholder="Ex: Sofá de três lugares"
                            required
                        >

                    </div>

                    <!-- ESTOQUE -->

                    <div class="adm-cadastro-campo">

                        <label for="cad_movel_estoque">
                            Estoque disponível
                        </label>

                        <input
                            type="number"
                            name="movel_estoque"
                            id="cad_movel_estoque"
                            min="0"
                            placeholder="Quantidade"
                            required
                        >

                    </div>

                    <!-- PREÇO -->

                    <div class="adm-cadastro-campo">

                        <label for="cad_movel_preco">
                            Preço do móvel
                        </label>

                        <input
                            type="number"
                            name="movel_preco"
                            id="cad_movel_preco"
                            step="0.01"
                            min="0"
                            placeholder="Ex: 1299.90"
                            required
                        >

                    </div>

                    <!-- CATEGORIA -->

                    <div class="adm-cadastro-campo">

                        <label for="cad_categoria">
                            Categoria do móvel
                        </label>

                        <select
                            name="categoria"
                            id="cad_categoria"
                            required
                        >

                            <option value="1">Cozinha</option>
                            <option value="2">Sala de Estar</option>
                            <option value="3">Quarto</option>
                            <option value="4">Diversos</option>

                        </select>

                    </div>

                    <!-- DESCRIÇÃO -->

                    <div class="adm-cadastro-campo adm-cadastro-campo-completo">

                        <label for="cad_movel_descricao">
                            Descrição do móvel
                        </label>

                        <textarea
                            name="movel_descricao"
                            id="cad_movel_descricao"
                            rows="4"
                            placeholder="Descreva as características do móvel..."
                            required
                        ></textarea>

                    </div>

                </div>

                <!-- IMAGEM -->

                <div class="adm-cadastro-imagem-area">

                    <div class="adm-cadastro-imagem-cabecalho">

                        <h3>Imagem do produto</h3>

                        <p>
                            Escolha uma foto para representar este móvel
                            no catálogo da loja.
                        </p>

                    </div>

                    <div class="adm-cadastro-imagem-preview">

                        <div id="imagem-padrao">
                            🪑
                        </div>

                        <img
                            id="imagem-preview"
                            src=""
                            alt="Prévia da imagem do móvel"
                            style="display: none;"
                        >

                    </div>

                    <label
                        for="cad_movel_imagem"
                        class="adm-cadastro-botao-imagem"
                    >
                        📷 Escolher imagem
                    </label>

                    <input
                        type="file"
                        name="movel_imagem"
                        id="cad_movel_imagem"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        required
                    >

                    <small class="adm-cadastro-ajuda">
                        Formatos aceitos: JPG, PNG, GIF e WEBP.
                        Tamanho máximo recomendado: 5 MB.
                    </small>

                    <p
                        id="nome-arquivo-imagem"
                        class="adm-cadastro-nome-arquivo"
                    >
                        Nenhuma imagem selecionada.
                    </p>

                </div>

                <!-- BOTÃO -->

                <button
                    type="submit"
                    class="adm-cadastro-botao-salvar"
                >
                    CADASTRAR MÓVEL
                </button>

            </form>

        </section>

        <div class="adm-cadastro-rodape">

            <a href="AdmMoveis.php">
                ← Voltar para administração de móveis
            </a>

        </div>

    </main>

    <!-- PRÉ-VISUALIZAÇÃO DA IMAGEM -->

    <script>

        const campoImagem = document.getElementById('cad_movel_imagem');
        const imagemPreview = document.getElementById('imagem-preview');
        const imagemPadrao = document.getElementById('imagem-padrao');
        const nomeArquivo = document.getElementById('nome-arquivo-imagem');

        campoImagem.addEventListener('change', function () {

            const arquivo = this.files[0];

            if (!arquivo) {
                return;
            }

            const tiposPermitidos = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp'
            ];

            if (!tiposPermitidos.includes(arquivo.type)) {

                alert('Selecione uma imagem JPG, PNG, GIF ou WEBP.');

                this.value = '';
                return;

            }

            if (arquivo.size > 5 * 1024 * 1024) {

                alert('A imagem não pode ultrapassar 5 MB.');

                this.value = '';
                return;

            }

            const leitor = new FileReader();

            leitor.onload = function (evento) {

                imagemPreview.src = evento.target.result;

                imagemPreview.style.display = 'block';

                imagemPadrao.style.display = 'none';

            };

            leitor.readAsDataURL(arquivo);

            nomeArquivo.textContent = arquivo.name;

        });

    </script>

</body>

</html>
