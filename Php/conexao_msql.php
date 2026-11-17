<?php

$sql = "";

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

if($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}else{
    //die("conectado com sucesso");
}

$conexao->set_charset("utf8");

?>

