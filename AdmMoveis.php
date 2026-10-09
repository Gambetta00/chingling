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

        <a href="Moveis.php">Produtos</a>

        <a href="AdmMoveis.php">Administração de Produtos (ADM ONLY)</a>

        <a href="AdmUsuarios.php">Administração de Usuários (ADM ONLY)</a>

    </nav>


    <main>

        <h1>CADASTRO DE MÓVEIS</h1>

        <p class="descricao">
            Preencha os dados abaixo para cadastrar um móvel na loja.
        </p>


        <div class="caixa">

            <h2>Dados do móvel</h2>

            <?php
            session_start();

            if (isset($_SESSION['mensagem'])) {
                echo $_SESSION['mensagem'];
                unset($_SESSION['mensagem']);
            }
            ?>

            <form method="POST" action="AdmMoveis_back.php">

                <label for="movel_nome">Nome do móvel</label>

                <input
                    type="text"
                    name="movel_nome"
                    id="movel_nome"
                    required
                >

                <br><br>


                <label for="movel_estoque">Estoque do móvel</label>

                <input
                    type="number"
                    name="movel_estoque"
                    id="movel_estoque"
                    min="0"
                    required
                >

                <br><br>


                <label for="movel_preco">Preço do móvel</label>

                <input
                    type="number"
                    name="movel_preco"
                    id="movel_preco"
                    step="0.01"
                    min="0"
                    required
                >

                <br><br>

                <label for="categoria">Categoria do móvel</label>

                <select name="categoria" id="categoria" required>

                    <option value="1">Cozinha</option>

                    <option value="2">Sala de Estar</option>

                    <option value="3">Quarto</option>

                    <option value="4">Diversos</option>

                </select>

                <br><br>


                <label for="movel_imagem">Imagem do móvel</label>

                <input
                    type="text"
                    name="movel_imagem"
                    id="movel_imagem"
                    required
                >

                <br><br>


                <label for="movel_descricao">Descrição do móvel</label>

                <input
                    type="text"
                    name="movel_descricao"
                    id="movel_descricao"
                    required
                >

                <br><br>


                <button type="submit" class="botao-alterar">
                    SALVAR
                </button>

                <br><br>

            </form>

        </div>

    </main>

</body>

</html>


