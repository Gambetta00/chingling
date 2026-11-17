
<?php
session_start();

require_once "conexao_msql.php";
require_once "Classe_Moveis.php";

if (empty($_SESSION['email'])) {
    header("Location: Login.php");
    exit();
}

$emailUsuario = $_SESSION['email'];

$sqlAdmin = "SELECT tipo FROM usuarios WHERE email = ?";
$stmtAdmin = $conexao->prepare($sqlAdmin);
$stmtAdmin->bind_param("s", $emailUsuario);
$stmtAdmin->execute();
$resultadoAdmin = $stmtAdmin->get_result();
$usuarioAdmin = $resultadoAdmin->fetch_assoc();
$stmtAdmin->close();

if (!$usuarioAdmin || $usuarioAdmin['tipo'] !== 'admin') {
    header("Location: Painel.php");
    exit();
}

if (
    !isset($_POST['movel_nome']) ||
    !isset($_POST['movel_estoque']) ||
    !isset($_POST['movel_preco']) ||
    !isset($_POST['categoria']) ||
    !isset($_POST['movel_descricao'])
) {
    $_SESSION['mensagem'] = "Preencha todos os campos.";
    header("Location: AdmCadastroMoveis.php");
    exit();
}

$nome = trim($_POST['movel_nome']);
$estoque = filter_input(INPUT_POST, 'movel_estoque', FILTER_VALIDATE_INT);
$preco = str_replace(',', '.', $_POST['movel_preco']);
$categoria = filter_input(INPUT_POST, 'categoria', FILTER_VALIDATE_INT);
$descricao = trim($_POST['movel_descricao']);

if (
    $nome === '' ||
    $estoque === false ||
    $estoque === null ||
    $estoque < 0 ||
    !is_numeric($preco) ||
    (float)$preco < 0 ||
    !$categoria ||
    $descricao === ''
) {
    $_SESSION['mensagem'] = "Verifique os dados informados.";
    header("Location: AdmCadastroMoveis.php");
    exit();
}

$preco = (float)$preco;

if (
    !isset($_FILES['movel_imagem']) ||
    $_FILES['movel_imagem']['error'] !== UPLOAD_ERR_OK
) {
    $_SESSION['mensagem'] = "Envie uma imagem válida para o móvel.";
    header("Location: AdmCadastroMoveis.php");
    exit();
}

$arquivo = $_FILES['movel_imagem'];

if ($arquivo['size'] > 5 * 1024 * 1024) {
    $_SESSION['mensagem'] = "A imagem deve ter no máximo 5 MB.";
    header("Location: AdmCadastroMoveis.php");
    exit();
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$tipoImagem = $finfo->file($arquivo['tmp_name']);

$tiposPermitidos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp'
];

if (!isset($tiposPermitidos[$tipoImagem])) {
    $_SESSION['mensagem'] = "Formato de imagem não permitido.";
    header("Location: AdmCadastroMoveis.php");
    exit();
}

$pastaImagens = __DIR__ . "/imagens/moveis/";

if (!is_dir($pastaImagens)) {
    if (!mkdir($pastaImagens, 0755, true)) {
        $_SESSION['mensagem'] = "Não foi possível criar a pasta de imagens.";
        header("Location: AdmCadastroMoveis.php");
        exit();
    }
}

$extensao = $tiposPermitidos[$tipoImagem];
$nomeImagem = bin2hex(random_bytes(16)) . "." . $extensao;

$caminhoCompleto = $pastaImagens . $nomeImagem;

$imagem = "imagens/moveis/" . $nomeImagem;

$imagem = "imagens/moveis/" . $nomeImagem;

>>>>>>> d30585d8396c3d0154b03bd9db5aa704cfffccb5
if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
    $_SESSION['mensagem'] = "Não foi possível salvar a imagem.";
    header("Location: AdmCadastroMoveis.php");
    exit();
}

$Movel = new Movel(
    $nome,
    $estoque,
    $preco,
    $categoria,
    $imagem,
    $descricao,
    $conexao
);

if ($Movel->Set_Movel(
    $nome,
    $estoque,
    $preco,
    $categoria,
    $imagem,
    $descricao,
    $conexao
)) {
    $_SESSION['mensagem'] = "Móvel cadastrado com sucesso!";
} else {

    if (file_exists($caminhoCompleto)) {
        unlink($caminhoCompleto);
    }

    if (empty($_SESSION['mensagem'])) {
        $_SESSION['mensagem'] = "Erro ao cadastrar o móvel.";
    }
}

header("Location: AdmCadastroMoveis.php");
exit();
?>

