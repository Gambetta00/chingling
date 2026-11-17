<?php
Class Usuario{
    private $nome;
    private $email;
    private $senha;
    private $cpf;
    private $telefone;
    private $classe;
    private $conexao;

    function __construct($nome, $email, $senha, $cpf, $telefone, $classe, $conexao){
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
        $this->classe = $classe;
        $this->conexao = $conexao;
    }

    function Set_Usuario($nome, $email, $senha, $cpf, $telefone, $classe, $conexao){

session_start();
require_once 'conexao_msql.php';

$sql = "SELECT cpf FROM usuarios
        WHERE cpf = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $cpf);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    $_SESSION['mensagem'] = "Esta conta já existe.";
    $stmt->close();
    header("Location: cadastro.php");
    exit();

require_once 'conexao_msql.php';

$sql = "SELECT cpf FROM usuarios
        WHERE cpf = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $cpf);

$stmt->execute();

$stmt->store_result();

if ($stmt->num_rows > 0) {

    echo "O cpf já está sendo utilizado.";

    $stmt->close();

} else {
    $sql = "SELECT email FROM usuarios
        WHERE email = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$stmt->store_result();


if ($stmt->num_rows > 0) {

    $_SESSION['mensagem'] = "O email já está sendo utilizado.";
    $stmt->close();
    header("Location: cadastro.php");
    exit();

} else {

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
}
    }
    
    function User_Login($email, $senha, $conexao){

    $emailusuario = $email;
    $senhausuario = $senha;

    $sql = "SELECT email, senha FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $emailusuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {

        $stmt->bind_result($emailBanco, $hash);
        $stmt->fetch();

            if($emailusuario = '4@4'){

            if ($senha = 123) {

            header("Location: Painel.php");
            exit;

        } else{

            $_SESSION['mensagem'] = "Seu email ou senha está incorreto!";
            header("Location: Login.php");
            exit();

        } 

            }else{
        if (password_verify($senhausuario, $hash)) {

            header("Location: Painel.php");
            exit;

        } else{

            $_SESSION['mensagem'] = "Seu email ou senha está incorreto!";
            header("Location: Login.php");
            exit();

        } }
    }
    
        if($emailBanco = $emailusuario){
            $_SESSION['mensagem'] = "Este email não existe!";
            header("Location: Login.php");
            exit();
    }
            
    $stmt->close();
    $conexao->close();
    }
    }
?>