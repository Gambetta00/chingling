<?php
session_start();

if(isset($email)){

$email = $_SESSION['email'];

require_once "conexao_msql.php";

$sql = "SELECT nome FROM usuarios WHERE email = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$stmt->store_result();

$stmt->bind_result($nome);

$stmt->fetch();


echo "Seja bem vindo $nome !";
}else{
    echo "Seja bem vindo!";
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inicio</title>

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

    <main class="painel">

        <h1>Início</h1>

        <p class="descricao">
            Acesse as opções de gerenciamento da loja Chingling.
        </p>

        <div class="caixa painel-caixa">

            <h2>Gerenciamento</h2>

            <p class="texto">
                Escolha uma das opções abaixo para continuar.
            </p>

            <div class="botoes">

            </div>

        </div>

    </main>

</body>

</html>

