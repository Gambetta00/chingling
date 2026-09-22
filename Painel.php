<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="Login.php" method="POST">
    <!-- Esse campo oculto envia o "sinal" -->
    <input type="hidden" name="sinal_clique" value="ativado">
    
    <button type="submit">Logout</button>
</form>
<form action="AdmProdutos.php" method="POST">
    <!-- Esse campo oculto envia o "sinal" -->
    <input type="hidden" name="clique" value="ativado">
    
    <button type="submit">Administrar produtos (ADM)</button>
</form>
</body>
</html>