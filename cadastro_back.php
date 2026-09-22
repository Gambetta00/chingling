<?php

require_once 'conexao_msql.php';

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

