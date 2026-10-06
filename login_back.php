<?php
session_start();

require_once 'conexao_msql.php';

if (isset($_POST['sinal_clique'])) {

    $_POST = array();

    header('Location: Login.php');
    exit();

} else {
    if (isset($_POST['email'])) {

        require_once 'Classe.php';

        $email = $_POST["email"];
        $senha = $_POST["senha"];

        $_SESSION['email'] = $email;
        $_SESSION['senha'] = $senha;

        $usuario = new Usuario("", $email, $senha, "", "", "", $conexao);

        $usuario->User_Login($email, $senha, $conexao);
    }
}



?>