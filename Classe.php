<?php
Class Usuario{
    private $nome;
    private $email;
    private $senha;
    private $cpf;
    private $telefone;
    private $classe;

    function __construct($nome, $email, $Senha_hash, $cpf, $telefone, $classe){
        $this->nome = $nome;
        $this->email = $email;
        $this->Senha_hash = $Senha_hash;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
        $this->classe = $classe;
    }

    function Set_Usuario(){

require_once 'conexao_msql.php';

$sql = "SELECT email FROM usuarios
        WHERE email = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$stmt->store_result();


if ($stmt->num_rows > 0) {

    echo "O email já está sendo utilizado.";

    $stmt->close();

} else {

    // EMAIL NÃO EXISTE, ENTÃO CADASTRA

    $stmt->close();

    $sql = "INSERT INTO usuarios (nome, email, senha, cpf, telefone, tipo)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $Senha_hash = password_hash(
        $senha,
        PASSWORD_DEFAULT
    );

    $stmt->bind_param(
        "ssssss",
        $nome,
        $email,
        $Senha_hash,
        $cpf,
        $telefone,
        $classe
    );

    $stmt->execute();

    $stmt->close();

    header("Location: Login.php");
    exit();
}
    }
    function Get_Usuario(){
        $
    }
    

}



?>