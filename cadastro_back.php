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

    $sql = "SELECT email FROM usuarios
            WHERE email = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows === 0){
    header("Location: Login.php");
    exit();
    }else{
        header('Location: cadastro.php');
        exit;
    }

    $stmt->close();
?>