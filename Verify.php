<?php

session_start();

if (isset($_POST['Lemail'], $_POST['Lsenha'])) {

    $emailusuario = $_POST['Lemail'];
    $senhausuario = $_POST['Lsenha'];

    $conexao = new mysqli(
        "localhost",
        "root",
        "",
        "chingling"
    );

    $sql = "SELECT email, senha FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $emailusuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

        $stmt->bind_result($emailBanco, $hash);
        $stmt->fetch();

        if (password_verify($senhausuario, $hash)) {

            $_SESSION['email'] = $emailBanco;

            header("Location: Painel.php");
            exit;

        } else {

            echo "Senha incorreta.";

        }

    } else {

        echo "Email não encontrado.";

    }

    $stmt->close();
    $conexao->close();
}
?>
