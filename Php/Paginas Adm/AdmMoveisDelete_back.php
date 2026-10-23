<?php

require_once 'conexao_msql.php';
$conexao ;

echo $_POST['delete_id'];

require_once "Classe_Moveis.php";

if (Movel :: UnSet_Movel($_POST['delete_id'], $conexao)) {
    echo "Móvel deletado com sucesso!";
} else {
    echo "Erro ao cadastrar usuário.";
}

?>