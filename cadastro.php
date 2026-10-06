<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro</title>

    <link rel="stylesheet" href="style2.css">
</head>

<body>

    <header>

        <div class="logo">
            <span>🛒</span>
            Chingling
        </div>

        <div class="usuario">
            Já possui uma conta?
            <a href="login.php">Fazer Login</a>
        </div>

    </header>


    <nav>
        <a href="Painel.php">Início</a>
    </nav>


    <main>

        <h1>CADASTRO</h1>

        <p class="descricao">
            Preencha os dados abaixo para criar sua conta.
        </p>


        <div class="caixa">

            <h2>Dados do usuário</h2>

            <form method="POST" action="cadastro_back.php">

                <label for="nome">Nome de usuário</label>

                <input 
                    type="text" 
                    name="nome" 
                    id="nome"
                    required
                >

                <br><br>


                <label for="email">Email</label>

                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    required
                >

                <br><br>


                <label for="classe">Escolha uma opção:</label>

                <select name="classe" id="classe">

                    <option value="cliente">
                        Usuário
                    </option>

                    <option value="admin">
                        Administrador
                    </option>

                </select>

                <br><br>


                <label for="senha">Senha</label>

                <input 
                    type="password" 
                    name="senha" 
                    id="senha"
                    required
                >

                <br><br>


                <label for="cpf">CPF</label>

                <input 
                    type="text" 
                    name="cpf" 
                    id="cpf"
                    required
                >

                <br><br>


                <label for="telefone">Telefone</label>

                <input 
                    type="text" 
                    name="telefone" 
                    id="telefone"
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