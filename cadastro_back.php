<?php

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

 if (isset($_POST['email'])){
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $classe = $_POST['classe'];
 }else echo "ERRO";



  $sql = "INSERT INTO usuarios (email, senha, tipo)
            VALUES (?, ?, ?)";

            $stmt = $conexao->prepare($sql);

            $Senha_hash = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    $stmt->bind_param(
        "sss",
        $email,
        $Senha_hash,
        $classe
    );

    $stmt->execute();
    $stmt->close();

    header("Location: Login.php");
    exit();

