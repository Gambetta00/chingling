<?php
session_start();
require_once 'conexao_msql.php';
$nome = $_POST['nome'];
$email = $_POST["email"];
$senha = $_POST["senha"];
$cpf = $_POST['cpf'];
$telefone = $_POST['telefone'];
$classe = "cliente";

$_SESSION['tipo'] = $classe;

require_once 'Classe.php';

$usuario = new Usuario($nome, $email, $senha, $cpf, $telefone, $classe, $conexao);

if ($usuario->Set_Usuario($nome, $email, $senha, $cpf, $telefone, $classe, $conexao)) {
    $_SESSION['mensagem'] = "Usuário cadastrado com sucesso";
    header("Location: Login.php");
    exit();
} else {
    $_SESSION['mensagem'] = "Erro ao cadastrar usuário";
    header("Location: Login.php");
    exit();
}

?>