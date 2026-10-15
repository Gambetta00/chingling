<?php
require_once 'conexao_msql.php';

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";

    $resultado = $conexao->query($sql);


            if (isset($resultado)) {
                if ($resultado->num_rows > 0) {
                    echo "Usuário encontrado!";
                    $usuario = $resultado->fetch_assoc();
                    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Usuário</title>

    <style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f5f7fa;
    color: #333;
}



header {
    height: 75px;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    border-bottom: 1px solid #ddd;
}

.logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 24px;
    color: #0875d1;
}

.logo span {
    font-size: 30px;
}

.pesquisa {
    display: flex;
    width: 400px;
}

.pesquisa input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 5px 0 0 5px;
}

.pesquisa button {
    width: 50px;
    border: none;
    background-color: #0875d1;
    color: white;
    border-radius: 0 5px 5px 0;
}

.usuario {
    color: #333;
    font-size: 14px;
}




nav {
    background-color: #006dcc;
    padding: 12px 30px;
    display: flex;
    gap: 25px;
}

nav a {
    color: white;
    text-decoration: none;
    font-size: 14px;
}



main {
    width: 90%;
    max-width: 1100px;
    margin: 30px auto;
}

h1 {
    color: #0875d1;
    font-size: 28px;
}

.descricao {
    margin-top: 8px;
    color: #666;
}



.caixa {
    background-color: white;
    margin-top: 25px;
    padding: 25px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.caixa h2 {
    margin-bottom: 20px;
    font-size: 19px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

.campo-pesquisa {
    display: flex;
    gap: 10px;
}

.campo-pesquisa input {
    flex: 1;
    padding: 13px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 15px;
}

.campo-pesquisa button {
    background-color: #0875d1;
    color: white;
    border: none;
    padding: 0 25px;
    border-radius: 5px;
    cursor: pointer;
}

.campo-pesquisa button:hover {
    background-color: #005da8;
}



.resultado {
    background-color: white;
    margin-top: 20px;
    padding: 25px;
    border: 1px solid #ddd;
    border-radius: 8px;
}

.resultado h2 {
    color: #333;
    font-size: 19px;
    margin-bottom: 20px;
}

.usuario-card {
    display: flex;
    align-items: flex-start;
    gap: 25px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
}

.icone {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background-color: #e8f3ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 35px;
}

.dados {
    line-height: 1.8;
}

.dados p {
    border-bottom: 1px solid #eee;
    padding: 3px 0;
}

.dados strong {
    display: inline-block;
    width: 100px;
    color: #555;
}

.botao-alterar {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #0875d1;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

.botao-alterar:hover {
    background-color: #005da8;
}

    </style>
</head>

<body>

    <header>
        <div class="logo">
            <span>🏠</span>
            <strong>Chingling</strong>
        </div>

        <div class="pesquisa">
            <input type="text" placeholder="O que você procura hoje?">
            <button>🔍</button>
        </div>

        <div class="usuario">
            👤 Minha conta
        </div>
    </header>

    <nav>
        <a href="#">Sala de Estar</a>
        <a href="#">Sala de Jantar</a>
        <a href="#">Cozinha</a>
        <a href="#">Quarto</a>
        <a href="#">Banheiro</a>
        <a href="#">Móveis</a>
        <a href="#">Promoções</a>
    </nav>


    <main>



        <section class="resultado">

            <h2>Resultado da pesquisa</h2>

             <form  method="POST" enctype="multipart/form-data"> 
 
        <div class="campo-grupo"> 
            <label for="email">Confirme o Email</label> 
            <input  
                type="text"  
                id="email"  
                name="email"  
                placeholder="Digite o seu email" 
                required 
            > 
            
            <button type="submit" class="btn-principal">Buscar</button> 
        </div> 
     </form> 

            <?php
            if (isset($resultado)) {
                if ($resultado->num_rows > 0) {
                    echo "Usuário encontrado!";
                    $usuario = $resultado->fetch_assoc();
                    ?>

                    <div class="usuario-card">

                        <div class="icone">👤</div>

                        <div class="dados">

                            <p>
                                <strong>ID:</strong>
                                <?php echo $usuario["id"]; ?>
                            </p>

                            <p>
                                <strong>Nome:</strong>
                                <?php echo $usuario["nome"]; ?>
                            </p>

                            <p>
                                <strong>E-mail:</strong>
                                <?php echo $usuario["email"]; ?>
                            </p>

                            <p>
                                <strong>CPF:</strong>
                                <?php echo $usuario["cpf"]; ?>
                            </p>

                            <p>
                                <strong>Telefone:</strong>
                                <?php echo $usuario["telefone"]; ?>
                            </p>

                            <p>
                                <strong>Tipo:</strong>
                                <?php echo $usuario["tipo"]; ?>
                            </p>

                    
        </section>

    </main>
  <?php
                } else {
                    echo "ERRO!";
                }
            }
            ?>
</body>
</html>
