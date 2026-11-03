<?php

session_start();

$email = $_SESSION["email"];

require_once "conexao_msql.php";

$sql = "SELECT tipo FROM usuarios
        WHERE email = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($tipo);
$stmt->fetch();


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chingling - Cadastro</title>

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
            <span>Já possui uma conta?</span>
            <a href="Login.php">Fazer Login</a>
        </div>

    </div>


    <!-- MENU -->
    <nav id="menu-categorias">
        <?php if ($tipo === 'admin'): ?>
            <a href="AdmMoveis.php">Administração de Móveis</a>
            <a href="AdmUsuarios.php">Administração de Usuários</a>
            <a href="Painel.php">Início</a>
        <?php endif; ?>
    </nav>


    <!-- CONTEÚDO DO CADASTRO -->
    <main class="cadastro-container">

        <section class="cadastro-introducao">

            <span class="subtitulo-secao">FAÇA PARTE DA CHINGLING</span>

            <h1>Crie sua conta</h1>

            <p>
                Cadastre-se para aproveitar nossas ofertas e encontrar
                os móveis ideais para sua casa.
            </p>

        </section>


        <section class="cadastro-caixa">

            <div class="cadastro-cabecalho">

                <div class="cadastro-icone">👤</div>

                <div>
                    <h2>Dados do usuário</h2>
                    <p>Preencha todos os campos para criar sua conta.</p>
                </div>

            </div>


            <form method="POST" action="cadastro_back.php" class="form-cadastro">

                <div class="campo-cadastro">
                    <label for="nome">Nome de usuário</label>

                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        placeholder="Digite seu nome"
                        required
                    >
                </div>


                <div class="campo-cadastro">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="exemplo@email.com"
                        required
                    >
                </div>


                <div class="campo-cadastro">
                    <label for="senha">Senha</label>

                    <input
                        type="password"
                        name="senha"
                        id="senha"
                        placeholder="Crie uma senha"
                        required
                    >
                </div>


                <div class="campo-cadastro">
                    <label for="cpf">CPF</label>

                    <input
                        type="text"
                        name="cpf"
                        id="cpf"
                        placeholder="Digite seu CPF"
                        required
                    >
                </div>


                <div class="campo-cadastro campo-cadastro-completo">
                    <label for="telefone">Telefone</label>

                    <input
                        type="text"
                        name="telefone"
                        id="telefone"
                        placeholder="Digite seu telefone"
                        required
                    >
                </div>


                <button type="submit" class="botao-cadastrar">
                    Criar minha conta →
                </button>

            </form>


            <div class="cadastro-rodape">
                Já possui uma conta?
                <a href="Login.php">Entre aqui</a>
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