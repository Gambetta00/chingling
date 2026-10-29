
<?php

session_start();

require_once 'conexao_msql.php';
$conexao ;

if(isset($_POST['movel_nome'])){
    $nome = $_POST['movel_nome'];
    $estoque = $_POST['movel_estoque'];
    $preco = $_POST['movel_preco'];
    $categoria = $_POST['categoria'];
    $imagem = $_POST['movel_imagem'];
    $descricao = $_POST['movel_descricao'];

} else {
    echo "ERRO";
    exit();
}

require_once "Classe_Moveis.php";

$Movel = new Movel($nome, $estoque, $preco, $categoria, $imagem, $descricao, $conexao);

if ($Movel->Set_Movel($nome, $estoque, $preco, $categoria, $imagem, $descricao, $conexao)) {
    echo "Usuário cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário.";
}

?>

