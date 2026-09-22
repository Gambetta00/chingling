
<?php

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

 if (isset($_POST['email'])){
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $classe = $_POST['classe'];
 }else echo "ERRO";



// VERIFICAR SE O EMAIL JÁ EXISTE

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

?>

