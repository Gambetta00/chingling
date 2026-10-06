<?php

require_once 'conexao_msql.php';

if (isset($_POST['sinal_clique'])) {

    $_POST = array();

    header('Location: Login.php');
    exit();

} else {

    if (isset($_POST['Lemail'])) {

        $Lemail = $_POST['Lemail'];
        $Lsenha = $_POST['Lsenha'];

        $sql = "SELECT email, senha FROM usuarios WHERE email = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param("s", $Lemail);

        $stmt->execute();

        $chingling = $stmt->get_result()->fetch_assoc();

        if ($chingling && 
            password_verify($Lsenha, $chingling["senha"])) {

            echo "Login com sucesso";

        } else {

            echo "Erro no login";

        }

    }
}

?>