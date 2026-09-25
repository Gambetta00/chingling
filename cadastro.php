 
 <!DOCTYPE html>

<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cadastro</title>

<link rel="stylesheet" href="./style.css">

</head>

<body>

<div id="cabecalho">

<div class="topo-esquerda">

    <div>🚚 Entrega para todo o Brasil | ✔️ Compra segura | ⭐ Qualidade e conforto para o seu lar</div>

</div>

<div class="topo-direita">

    <div>
        <a href="Conta.php">👤 Minha conta</a>
    </div>

</div>

</div>



<div id="barra-principal">
        <div class="logo">
            🏠 Chingling
            <span>Moveis para todos os momentos</span>
        </div>
        <div class="busca">
            <input type="text" placeholder="Olá, o que você procura hoje?">
            <button>🔍</button>
        </div>
        <div class="cep">
            📍 Informe seu CEP
        </div>
        <nav id="menu-categorias">


    

</div>


<div class="navegacao">

    <a href="#">Início</a>
    <a href="#">Produtos</a>
    <a href="#">Categorias</a>
    <a href="#">Ofertas</a>
    <a href="#">Contato</a>

</div>


<div id="contBanner">

    <form method="POST" action="cadastro_back.php">

        <h1>CADASTRO AQUI</h1>

        <label for="nome">Nome de usuário</label>
        <br>
        <input type="text" name="nome" id="nome" required>

        <br><br>

        <label for="email">Email</label>
        <br>
        <input type="text" name="email" id="email" required>

        <br><br>

        <label for="classe">Escolha uma opção:</label>
        <br>

        <select name="classe" id="classe">
            <option value="cliente">Usuário</option>
            <option value="admin">Administrador</option>
        </select>

        <br><br>

        <label for="senha">Senha</label>
        <br>
        <input type="password" name="senha" id="senha" required>

        <br><br>

        <label for="cpf">CPF</label>
        <br>
        <input type="text" name="cpf" id="cpf" required>

        <br><br>

        <label for="telefone">Telefone</label>
        <br>
        <input type="text" name="telefone" id="telefone" required>

        <br><br>

        <button type="submit">SALVAR</button>

        <br><br>

        <a href="login.php">Fazer Login</a>

    </form>

</div>


</body>

</html>

