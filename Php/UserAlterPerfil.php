
<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


session_start();

require_once 'conexao_msql.php';

$emailOriginal = $_SESSION['email'] ?? null;

if (!$emailOriginal) {
    exit('Usuário não identificado.');
}

function buscarUsuario(mixed $conexao, mixed $email) {

    $sql = "SELECT * FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    return $stmt->get_result();
}



$resultado = buscarUsuario($conexao, $emailOriginal);

$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    exit('Usuário não encontrado.');
}

$salvou = false;
$erro = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {    


    $nome = !empty($_POST['nome'])
        ? $_POST['nome']
        : $usuario['nome'];

    $telefone = !empty($_POST['telefone'])
        ? $_POST['telefone']
        : $usuario['telefone'];

    $emailN = !empty($_POST['emailN'])
        ? $_POST['emailN']
        : $usuario['email'];

    $senha = !empty($_POST['senha'])
        ? password_hash($_POST['senha'], PASSWORD_DEFAULT)
        : $usuario['senha'];

    $imagem = $usuario['imagem'];

    if (
        isset($_FILES['imagem']) &&
        $_FILES['imagem']['error'] === UPLOAD_ERR_OK
    ) {

        $extensao = strtolower(
            pathinfo(
                $_FILES['imagem']['name'],
                PATHINFO_EXTENSION
            )
        );

        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extensao, $permitidas, true)) {
            exit('Formato de imagem não permitido.');
        }

        $pasta = __DIR__ . '/imagens/';

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $nomeImagem = uniqid('img_', true) . '.' . $extensao;

        $destino = $pasta . $nomeImagem;

        if (
            move_uploaded_file(
                $_FILES['imagem']['tmp_name'],
                $destino
            )
        ) {

            $imagem = 'imagens/' . $nomeImagem;

        } else {

            exit('Erro ao salvar imagem.');

        }
    }

 

    $sql = "UPDATE usuarios
            SET nome = ?,
                telefone = ?,
                email = ?,
                senha = ?,
                imagem = ?
            WHERE email = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ssssss",
        $nome,
        $telefone,
        $emailN,
        $senha,
        $imagem,
        $emailOriginal
    );

    try {

        $stmt->execute();

        $salvou = true;

        $_SESSION['emailAlterar'] = $emailN;

        $emailOriginal = $emailN;

        $resultado = buscarUsuario($conexao, $emailOriginal);

        $usuario = $resultado->fetch_assoc();

    } catch (mysqli_sql_exception $e) { // Mudado de $erro para $e

        $salvou = false;
        
        // Verifica se é erro de chave duplicada (ex: e-mail já cadastrado no MySQL código 1062)
        if ($e->getCode() === 1062) {
            $erro = "Este e-mail já está em uso por outro usuário.";
        } else {
            $erro = "Ocorreu um problema ao atualizar os dados. Tente novamente mais tarde.";
        }
        

    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Alterar Usuário - Chingling</title>

<link rel="stylesheet" href="style.css">

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
    cursor: pointer;
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

.main-centralizado {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 20px;
}

.card-alterar {
    background-color: #ffffff;
    width: 100%;
    max-width: 450px;
    padding: 35px 30px;
    border: 1px solid #e1e8ed;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    text-align: center;
}

.card-alterar h2 {
    color: #0d2c54;
    font-size: 22px;
    margin-top: 15px;
    margin-bottom: 5px;
}

.card-alterar .subtitulo {
    color: #666;
    font-size: 14px;
    margin-bottom: 25px;
}

.icone-perfil {
    width: 70px;
    height: 70px;
    background-color: #0875d1;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin: 0 auto;
}

.campo-grupo {
    text-align: left;
    margin-bottom: 18px;
}

.campo-grupo label {
    display: block;
    font-size: 13px;
    font-weight: bold;
    color: #333;
    margin-bottom: 6px;
}

.campo-grupo label small {
    font-weight: normal;
    color: #777;
}

.campo-grupo input[type="text"],
.campo-grupo input[type="password"],
.campo-grupo input[type="file"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    outline: none;
}

.campo-grupo input[type="text"]:focus,
.campo-grupo input[type="password"]:focus {
    border-color: #0875d1;
}

.btn-principal {
    width: 100%;
    background-color: #0875d1;
    color: white;
    border: none;
    padding: 13px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

.btn-principal:hover {
    background-color: #005da8;
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

.imagem-usuario {
    width: 200px;
    height: 200px;
    object-fit: contain;
    display: block;
    margin: 10px auto;
    background-color: #f5f7fa;
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
        <button type="button">🔍</button>
    </div>

    <div class="usuario">
        👤 Minha Conta
    </div>

</header>


<nav>

    <a href="Painel.php">Início</a>
    <a href="Moveis.php">Móveis</a>
    <a href="AdmMoveis.php">Administração de Móveis (ADM ONLY)</a>
    <a href="AdmUsuarios.php">Administração de Usuários (ADM ONLY)</a>
    <a href="AdmCadastroMoveis.php">Cadastro de Móveis (ADM ONLY)</a>

</nav>


<main class="main-centralizado">

<section class="card-alterar">

    <div class="icone-perfil">
        <span>👤</span>
    </div>

    <h2>Alterar dados</h2>

    <p class="subtitulo">
        Atualize as informações do seu perfil de usuário.
    </p>

    

    <small>(Deixe em branco para não alterar)</small>

    <br><br>


    <!-- FORMULARIO -->

    <form method="POST" enctype="multipart/form-data">

        <div class="campo-grupo">

            <label for="nome">Nome completo</label>

            <input
                type="text"
                id="nome"
                name="nome"
                value="<?php echo htmlspecialchars($usuario['nome']); ?>"
            >

        </div>


        <div class="campo-grupo">

            <label for="telefone">Telefone</label>

            <input
                type="text"
                id="telefone"
                name="telefone"
                value="<?php echo htmlspecialchars($usuario['telefone']); ?>"
            >

        </div>


        <div class="campo-grupo">

            <label for="emailN">E-mail</label>

            <input
                type="text"
                id="emailN"
                name="emailN"
                value="<?php echo htmlspecialchars($usuario['email']); ?>"
            >

        </div>


        <div class="campo-grupo">

            <label for="senha">Nova senha</label>

            <input
                type="password"
                id="senha"
                name="senha"
                placeholder="Digite a nova senha"
            >

        </div>


        <div class="campo-grupo">

            <label for="imagem">Foto de Perfil</label>

            <input
                type="file"
                id="imagem"
                name="imagem"
                accept="image/*"
            >

        </div>


        <button type="submit" class="btn-principal">
            Salvar Alterações
        </button>

    </form>


    <!-- RESULTADO -->

    <?php if ($salvou): ?>

        <div class="usuario-card">

            <br>

            <h3>Usuário atualizado com sucesso!</h3>

            <p>
                <strong>Nome:</strong>
                <?php echo htmlspecialchars($usuario['nome']); ?>
            </p>

            <p>
                <strong>E-mail:</strong>
                <?php echo htmlspecialchars($usuario['email']); ?>
            </p>

            <p>
                <strong>Telefone:</strong>
                <?php echo htmlspecialchars($usuario['telefone']); ?>
            </p>

            <p>
                <strong>Imagem:</strong>

                <img
                    src="<?php echo htmlspecialchars($usuario['imagem']); ?>"
                    alt="Imagem do usuário"
                    class="imagem-usuario"
                >

            </p>

        </div>

    <?php endif; ?>


    <?php if (!empty($erro)): ?>

        <p><?php echo htmlspecialchars($erro); ?></p>

    <?php endif; ?>


</section>

</main>

</body>
</html>
