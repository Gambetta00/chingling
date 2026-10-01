
<?php

require_once 'Classe.php';

$email = $_POST["email"];
$senha = $_POST["senha"];
$tipo = $_POST["tipo"];

$usuario = new Usuario($email, $senha, $tipo);

if ($usuario->cadastrar($conexao)) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário.";
}

?>

