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
            <span>🏠</span>
            <h1>Chingling</h1>
        </div>

        <div class="usuario">
            Não possui uma conta?
            <a href="cadastro.php">Cadastrar</a>
        </div>

    </header>


    <nav>
         <a href="Painel.php">Início (ADM ONLY)</a>
    </nav>


    <main>

        <h1>LOGIN</h1>

        <p class="descricao">
            Preencha os dados abaixo para logar na sua conta.
        </p>


        <div class="caixa">

            <h2>Dados do usuário</h2>

            <form method="POST" action="login_back.php">
                <br><br>


                <label for="email">Email</label>

                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    required
                >

                <br><br>


                <label for="senha">Senha</label>

                <input 
                    type="password" 
                    name="senha" 
                    id="senha"
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