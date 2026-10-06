<?php
require_once 'conexao_msql.php';
$nome = $_POST['nome'];
$email = $_POST["email"];
$senha = $_POST["senha"];
$cpf = $_POST['cpf'];
$telefone = $_POST['telefone'];
$classe = $_POST["classe"];

require_once 'Classe.php';

$usuario = new Usuario($nome, $email, $senha, $cpf, $telefone, $classe, $conexao);

if ($usuario->Set_Usuario($nome, $email, $senha, $cpf, $telefone, $classe, $conexao)) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário.";
}

?>