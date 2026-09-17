<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Login AQUI</h1>

<form method="POST" action='login.php'>
    <label for="Lemail">Seu Email</label> <br>

    <input type="text" name="Lemail"  required> <br> <br>


     <label for="Lsenha">Sua Senha</label> <br>

    <input type="password" name="Lsenha"  required> <br> <br>

    <button type="submit">SALVAR</button> <br> <br>

        <a href="cadastro.php">Cadastrar</a>
</body>
</html>

<?php
session_start();

if(isset($_POST['Lemail'] & $_POST['Lsenha'])){
    $email = $_POST['Lemail'];
    $senha = $_POST['Lsenha'];

    $_SESSION['email'] = $email;
    $_SESSION['senha'] = $senha;
}

# Nota de Gambetta : Este código abaixo serve para verificar se o email do usuário está registrado no banco de dados.

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "chingling";

$conexao = new mysqli(
    $servidor,
    $usuario,
    $senha,
    $banco
);

$sql = "
    SELECT email FROM usuarios 
    WHERE email = ? 
";



$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0){
    # Email encontrado. Agora, fazer o código para pesquisar a senha no banco e ver se a senha está condizente com a senha do banco, se sim, dar acesso ao painel.
}else{
    echo "Email incorreto.";
}
?>