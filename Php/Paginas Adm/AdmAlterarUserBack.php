<?php

require_once 'conexao_msql.php';

if (isset($_POST['email'])) { 
    $nome = $_POST['nome']; 
    $telefone = $_POST['telefone']; 
    $senha = $_POST['senha']; 
    $imagem = $_POST['imagem']; 
    $email = $_POST['email']; 

    $sql = "UPDATE usuarios
SET nome = '$nome',
telefone = '$telefone',
senha = '$senha',
imagem = '$imagem'
WHERE email = '$email';
"; 
 
    $resultado = $conexao->query($sql); 
} 



?>