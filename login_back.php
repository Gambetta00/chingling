<?php
session_start();

require_once 'conexao_msql.php';

if (isset($_POST['sinal_clique'])) {

    $_POST = array();

    header('Location: Login.php');
    exit();

} else {
    if (isset($_POST['Lemail'])) {

        require_once 'Classe.php';

        $email = $_POST["Lemail"];
        $senha = $_POST["Lsenha"];

        $usuario = new Usuario("", $email, $senha, "", "", "", $conexao);

        $usuario->User_Login($email, $senha);
    }
}

$_SESSION['email'] = $email;
$_SESSION['senha'] = $senha;

?>