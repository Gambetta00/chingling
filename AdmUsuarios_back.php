<?php 

require_once 'conexao_msql.php';

 if (isset($_POST['Pemail'])){
    $Pemail = $_POST['Pemail'];
 }


$sql = "SELECT * FROM usuarios WHERE email = '$Pemail'";

$resultado = $conexao->query($sql);

if ($resultado->num_rows > 0) {
    echo "Usuário encontrado!";
} else {
    echo "Usuário não encontrado!";
}

$usuario = $resultado->fetch_assoc();

    echo "<br>ID: " . $usuario["id"] . "<br>";
echo "Nome: " . $usuario["nome"] . "<br>";
echo "Email: " . $usuario["email"]. "<br>";
    echo "Cpf: " . $usuario["cpf"] . "<br>";
echo "Telefone: " . $usuario["telefone"] . "<br>";
echo "Tipo: " . $usuario["tipo"]. "<br>";

?>