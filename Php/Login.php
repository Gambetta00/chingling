
<?php

session_start();

require_once "conexao_msql.php";

// Verifica se existe um usuário logado.
$tipo = null;

if (isset($_SESSION['email'])) {

    $email = $_SESSION['email'];

    $sql = "SELECT tipo FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($tipo);
    $stmt->fetch();
    $stmt->close();
}

// Recupera a mensagem de retorno do login.
$mensagem = "";

if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chingling - Login</title>

    <link rel="stylesheet" href="style2.css?v=5">
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
            <span>👤 Bem-vindo à Chingling!</span>
        </div>

    </div>


    <!-- CABEÇALHO PRINCIPAL -->
    <div id="barra-principal">

        <a href="Painel.php" class="logo">
            <span>🏠 Chingling</span>
            <small>Móveis para todos os momentos</small>
        </a>

        <div class="cadastro-acesso">
            <span>Não possui uma conta?</span>
            <a href="cadastro.php">Criar conta</a>
        </div>

    </div>


    <!-- MENU -->
    <nav id="menu-categorias">

        <?php if ($tipo === 'admin'): ?>

            <a href="AdmMoveis.php">Administração de Móveis</a>
            <a href="AdmUsuarios.php">Administração de Usuários</a>
            <a href="Painel.php">Início</a>

        <?php elseif (isset($_SESSION['email'])): ?>

            <a href="Painel.php">Início</a>
            <a href="Moveis.php">Móveis</a>

        <?php endif; ?>

    </nav>


    <!-- CONTEÚDO DO LOGIN -->
    <main class="cadastro-container">

        <section class="cadastro-introducao">

            <span class="subtitulo-secao">BEM-VINDO DE VOLTA</span>

            <h1>Entre na sua conta</h1>

            <p>
                Acesse sua conta Chingling e continue encontrando
                os móveis ideais para sua casa.
            </p>

        </section>


        <section class="cadastro-caixa">

            <div class="cadastro-cabecalho">

                <div class="cadastro-icone">👤</div>

                <div>
                    <h2>Dados do usuário</h2>
                    <p>Informe seus dados para acessar sua conta.</p>
                </div>

            </div>


            <!-- MENSAGEM DE ERRO -->
            <?php if ($mensagem !== ''): ?>

                <div class="mensagem-painel" role="alert">
                    <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>


            <!-- FORMULÁRIO DE LOGIN -->
            <form method="POST" action="login_back.php" class="form-cadastro">

                <div class="campo-cadastro campo-cadastro-completo">

                    <label for="email">Email</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="exemplo@email.com"
                        required
                    >

                </div>


                <div class="campo-cadastro campo-cadastro-completo">

                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        name="senha"
                        id="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                </div>


                <button type="submit" class="botao-cadastrar">
                    Entrar na minha conta →
                </button>

            </form>


            <div class="cadastro-rodape">
                Ainda não possui uma conta?
                <a href="cadastro.php">Cadastre-se aqui</a>
            </div>

        </section>


        <!-- BENEFÍCIOS -->
        <section class="cadastro-beneficios">

            <div>
                🚚
                <strong>Entrega nacional</strong>
            </div>

            <div>
                🔒
                <strong>Compra segura</strong>
            </div>

            <div>
                ⭐
                <strong>Qualidade Chingling</strong>
            </div>

        </section>

    </main>

</body>
</html>