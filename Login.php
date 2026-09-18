<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Login AQUI</h1>

<form method="POST" action='login.php'>
    <label for="Lemail">Seu Email</label> <br>

    <input type="text" name="Lemail"  required> <br> <br>


     <label for="Lsenha">Sua Senha</label> <br>

    <input type="password" name="Lsenha"  required> <br> <br>

    <button type="submit">SALVAR</button> <br> <br>

        <a href="cadastro.php">Cadastrar</a>
</body>
</html>
<?php
include_once("Verify.php");
exit;
?>
