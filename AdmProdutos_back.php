
<?php

session_start();

require_once 'conexao_msql.php';

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

$sql = "INSERT INTO produtos (nome, estoque, preco, categoria_id, imagem, descricao)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssssss",
    $nome,
    $estoque,
    $preco,
    $categoria,
    $imagem,
    $descricao
);

$stmt->execute();

$stmt->close();

// Mensagem que será enviada para a outra página
$_SESSION['mensagem'] = "Móvel cadastrado com sucesso!";

header("Location: AdmProdutos.php");
exit();

?>

