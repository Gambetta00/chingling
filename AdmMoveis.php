<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h1>CADASTRO DE MÓVEIS AQUI</h1>

<form method="POST" action='AdmProdutos_back.php'>
    <label for="movel_nome">Nome do móvel</label> <br>

    <input type="text" name="movel_nome"  required> <br> <br>

    <label for="movel_estoque">Estoque do móvel</label> <br>

    <input type="number" name="movel_estoque"  required> <br> <br>

    <label for="movel_preco">Preço do móvel</label> <br>

    <input type="number" name="movel_preco"  required> <br> <br>

    <label for="categoria">Categoria do móvel</label> <br>

<select name="categoria">
    <option value="1">Cozinha</option>
    <option value="2">Sala de Estar</option>
    <option value="3">Quarto</option>
    <option value="4">Diversos</option>
</select>
    <br> <br>
<label for="movel_imagem">Imagem do móvel (incompleto)</label> <br>

<input type="text" name="movel_imagem"  required> <br> <br>

<label for="movel_descricao">Descrição do móvel</label> <br>

<input type="text" name="movel_descricao"  required> <br> <br>

<br> <br>


    <button type="submit">SALVAR</button> <br> <br>

    <a href="Painel.php">Voltar ao painel</a>
</body>
</html>


<?php

session_start();

if(isset($_SESSION['mensagem'])){

    echo $_SESSION['mensagem'];

    unset($_SESSION['mensagem']);
}

?>

