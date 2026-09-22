<?php

require_once 'conexao_msql.php';
 if(isset($_POST['sinal_clique'])){
    $_POST = array();
    header('Location: Login.php');
    exit();
 } else{
 if (isset($_POST['lemail'])){
    $Lemail = $_POST['Lemail'];
    $Lsenha = $_POST['Lsenha'];
 }
  
 $sql = "select email, senha from usuarios where email = $Lemail";
 $stmt = $conexao->prepare($sql);
 $stmt->execute();
 $chingling = $stmt->get_result()->fetch_assoc();



    if( $Lemail == $chingling["Lemail"] and (password_verify($Lsenha, $chingling["Lsenha"])) ){
echo "login com Sucesso";
    }else{ echo "erro no login";}
}
?>