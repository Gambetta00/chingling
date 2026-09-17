<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h1>CADASTRO AQUI</h1>

<form method="POST" action='cadastro_back.php'>
    <label for="email">Email</label> <br>

    <input type="text" name="email"  required> <br> <br>
       
  <label>Escolha uma opção:</label> <br>

<select name="classe">
    <option value="cliente">Usuário</option>
    <option value="admin">Adiministrador</option>
</select>

<br> <br>


     <label for="senha">Senha</label> <br>

    <input type="password" name="senha"  required> <br> <br>

    <button type="submit">SALVAR</button> <br> <br>

    <a href="login.php">Fazer Logins</a>
</body>
</html>