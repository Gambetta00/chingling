<?php
require_once 'conexao_msql.php';

function buscarUsuario(mixed $conexao, mixed $email) {
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";

    $resultado = $conexao->query($sql);

    return $resultado;
}

$resultado = null;
$email = null;

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $resultado = buscarUsuario($conexao, $email);
}
?>
 
<!DOCTYPE html> 
<html lang="pt-br"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Alterar Usuário - Chingling</title> 
    <link rel="stylesheet" href="style.css"> 
 
<style> 
 
* { 
    box-sizing: border-box; 
    margin: 0; 
    padding: 0; 
} 
 
body { 
    font-family: Arial, sans-serif; 
    background-color: #f5f7fa; 
    color: #333; 
} 
 
header { 
    height: 75px; 
    background-color: white; 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    padding: 0 30px; 
    border-bottom: 1px solid #ddd; 
} 
 
.logo { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    font-size: 24px; 
    color: #0875d1; 
} 
 
.logo span { 
    font-size: 30px; 
} 
 
.pesquisa { 
    display: flex; 
    width: 400px; 
} 
 
.pesquisa input { 
    width: 100%; 
    padding: 12px; 
    border: 1px solid #ccc; 
    border-radius: 5px 0 0 5px; 
} 
 
.pesquisa button { 
    width: 50px; 
    border: none; 
    background-color: #0875d1; 
    color: white; 
    border-radius: 0 5px 5px 0; 
    cursor: pointer; 
} 
 
.usuario { 
    color: #333; 
    font-size: 14px; 
} 
 
nav { 
    background-color: #006dcc; 
    padding: 12px 30px; 
    display: flex; 
    gap: 25px; 
} 
 
nav a { 
    color: white; 
    text-decoration: none; 
    font-size: 14px; 
} 
 
.main-centralizado { 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    padding: 40px 20px; 
} 
 
.card-alterar { 
    background-color: #ffffff; 
    width: 100%; 
    max-width: 450px; 
    padding: 35px 30px; 
    border: 1px solid #e1e8ed; 
    border-radius: 12px; 
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); 
    text-align: center; 
} 
 
.card-alterar h2 { 
    color: #0d2c54; 
    font-size: 22px; 
    margin-top: 15px; 
    margin-bottom: 5px; 
} 
 
.card-alterar .subtitulo { 
    color: #666; 
    font-size: 14px; 
    margin-bottom: 25px; 
} 
 
.icone-perfil { 
    width: 70px; 
    height: 70px; 
    background-color: #0875d1; 
    color: white; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 32px; 
    margin: 0 auto; 
} 
 
.campo-grupo { 
    text-align: left; 
    margin-bottom: 18px; 
} 
 
.campo-grupo label { 
    display: block; 
    font-size: 13px; 
    font-weight: bold; 
    color: #333; 
    margin-bottom: 6px; 
} 
 
.campo-grupo label small { 
    font-weight: normal; 
    color: #777; 
} 
 
.campo-grupo input[type="text"], 
.campo-grupo input[type="password"], 
.campo-grupo input[type="file"] { 
    width: 100%; 
    padding: 12px; 
    border: 1px solid #ccc; 
    border-radius: 6px; 
    font-size: 14px; 
    outline: none; 
} 
 
.campo-grupo input[type="text"]:focus, 
.campo-grupo input[type="password"]:focus { 
    border-color: #0875d1; 
} 
 
.btn-principal { 
    width: 100%; 
    background-color: #0875d1; 
    color: white; 
    border: none; 
    padding: 13px; 
    border-radius: 6px; 
    font-size: 15px; 
    font-weight: bold; 
    cursor: pointer; 
    margin-top: 10px; 
} 
 
.btn-principal:hover { 
    background-color: #005da8; 
} 
 
.botao-alterar {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background-color: #0875d1;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

.botao-alterar:hover {
    background-color: #005da8;
}


</style> 
 
</head> 
<body> 
 
<header> 
    <div class="logo"> 
        <span>🏠</span> 
        <strong>Chingling</strong> 
    </div> 
 
    <div class="pesquisa"> 
        <input type="text" placeholder="O que você procura hoje?"> 
        <button type="button">🔍</button> 
    </div> 
 
    <div class="usuario"> 
        👤 Minha conta 
    </div> 
</header> 
 
<nav> 
    <a href="#">Sala de Estar</a> 
    <a href="#">Quarto</a> 
    <a href="#">Sala de Jantar</a> 
    <a href="#">Cozinha</a> 
    <a href="#">Escritório</a> 
    <a href="#">Banheiro</a> 
</nav> 
 
<main class="main-centralizado"> 
 
<section class="card-alterar"> 
     
    <div class="icone-perfil"> 
        <span>👤</span> 
    </div> 
 
    <h2>Alterar dados</h2> 
    <p class="subtitulo">Atualize as informações do seu perfil de usuário.</p> 
 
    <form  method="POST" enctype="multipart/form-data"> 
 
        <div class="campo-grupo"> 
            <label for="email">Confirme o Email</label> 
            <input  
                type="text"  
                id="email"  
                name="email"  
                placeholder="Digite o seu email" 
                required 
            > 
            
            <button type="submit" class="btn-principal">Buscar</button> 
        </div> 
     </form> 
        <?php 
 
        if (isset($resultado)) { 
 
            if ($resultado->num_rows > 0) { 
 
                echo "Usuário ".$email." selecionado!<br>"; 
 
                $usuario = $resultado->fetch_assoc(); 

 
        ?> 
 <br>
 <form  method="POST" enctype="multipart/form-data"> 
                <div class="campo-grupo"> 
                     <?php echo "Nome Atual: ".$usuario["nome"]; ?>
                    <label for="nome">Nome completo</label> 
                    <input  
                        type="text"  
                        id="nome"  
                        name="nome"  
                        placeholder="Digite o seu nome" 
                        required 
                    > 
                </div> 
 
                <div class="campo-grupo"> 
                    <?php echo "Telefone Atual: ".$usuario["telefone"]; ?>
                    <label for="telefone">Telefone</label> 
                    <input  
                        type="text"  
                        id="telefone"  
                        name="telefone"  
                        placeholder="(00) 00000-0000" 
                        required 
                    > 
                </div> 
 
                <div class="campo-grupo"> 
                    <label for="senha">Nova Senha <small>(Deixe em branco para não alterar)</small></label> 
                    <input  
                        type="password"  
                        id="senha"  
                        name="senha"  
                        placeholder="Digite a nova senha" 
                    > 
                </div> 
 
                <div class="campo-grupo"> 
                    <label for="imagem">Foto de Perfil</label> 
                    <input  
                        type="file"  
                        id="imagem"  
                        name="imagem"  
                        accept="image/*" 
                    > 
                </div> 

                <input
    type="hidden"
    name="email"
    value="<?php echo htmlspecialchars($usuario['email']); ?>"
>
 
                <button type="submit" class="btn-principal">Salvar Alterações</button> 
 
        <?php 
 
            } else { 
 
                echo "Pesquisar Novamente / Usuário Não encontrado<br>"; 
 
            } 
 
        } 


        function alterar(mixed $conexao){
 if (isset($_POST['nome'])) { 
    $nome = $_POST['nome']; 
    $telefone = $_POST['telefone']; 
    $senha = $_POST['senha']; 
    $email = $_POST['email']; 

    $sql = "UPDATE usuarios
SET nome = '$nome',
telefone = '$telefone',
imagem = '',
senha = '$senha'
WHERE email = '$email';
"; 
 
    $resultado2 = $conexao->query($sql); 
 
    return $resultado2;
 }


        }
        $resultado2 = null;
   $resultado2 = alterar($conexao);

         if (isset($resultado2)) { 
 
           
 ?>
<div class="usuario-card">

                        <div class="icone">👤</div>

                        <div class="dados">

                            
                            <p>
                                <strong>Nome:</strong>
                                <?php echo "NOVO: ". $usuario["nome"]; ?>
                            </p>

                            <p>
                                <strong>E-mail:</strong>
                                <?php echo "NOVO: ".$usuario["email"]; ?>
                            </p>

                            <p>
                                <strong>CPF:</strong>
                                <?php echo"NOVO: ". $usuario["senha"]; ?>
                            </p>

                            <p>
                                <strong>Telefone:</strong>
                                <?php echo"NOVO: ". $usuario["telefone"]; ?>

                        
                            </p>
                        </div>
                    <?php


 } 
 
        

?>
 </form> 

 
</section> 
 
</main> 
 
</body> 
</html>