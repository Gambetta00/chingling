
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();
require_once 'conexao_msql.php';

$emailOriginal = $_SESSION['email'] ?? null;

if (!$emailOriginal) {
    header("Location: Login.php");
    exit;
}

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function buscarUsuario(mysqli $conexao, string $email): ?array {
    $sql = "SELECT * FROM usuarios WHERE email = ?";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $usuario = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    return $usuario ?: null;
}

$usuario = buscarUsuario($conexao, $emailOriginal);

if (!$usuario) {
    session_destroy();
    header("Location: Login.php");
    exit;
}

$salvou = false;
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $emailN = trim($_POST['emailN'] ?? '');
    $senhaDigitada = $_POST['senha'] ?? '';

    // Campos vazios mantêm os valores anteriores.
    $nome = $nome !== '' ? $nome : $usuario['nome'];
    $telefone = $telefone !== '' ? $telefone : $usuario['telefone'];
    $emailN = $emailN !== '' ? $emailN : $usuario['email'];

    $senha = $senhaDigitada !== ''
        ? password_hash($senhaDigitada, PASSWORD_DEFAULT)
        : $usuario['senha'];

    $imagem = $usuario['imagem'];

    try {

        // Verifica se o e-mail informado é válido.
        if (!filter_var($emailN, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Informe um endereço de e-mail válido.");
        }

        // Verifica se outro usuário já utiliza esse e-mail.
        if ($emailN !== $emailOriginal) {

            $sqlVerificar = "
                SELECT id
                FROM usuarios
                WHERE email = ?
            ";

            $stmtVerificar = $conexao->prepare($sqlVerificar);
            $stmtVerificar->bind_param("s", $emailN);
            $stmtVerificar->execute();

            $emailExiste = $stmtVerificar->get_result()->num_rows > 0;

            $stmtVerificar->close();

            if ($emailExiste) {
                throw new Exception("Este e-mail já está em uso por outro usuário.");
            }
        }

        // Processamento da foto de perfil.
        if (
            isset($_FILES['imagem']) &&
            $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Não foi possível enviar a imagem.");
            }

            // Limite de 5 MB.
            if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) {
                throw new Exception("A imagem deve ter no máximo 5 MB.");
            }

            // Verifica o tipo real do arquivo.
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['imagem']['tmp_name']);
            finfo_close($finfo);

            $tiposPermitidos = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];

            if (!isset($tiposPermitidos[$mime])) {
                throw new Exception("Formato de imagem não permitido. Utilize JPG, PNG, GIF ou WEBP.");
            }

            $extensao = $tiposPermitidos[$mime];

            $pasta = __DIR__ . '/imagens/';

            if (!is_dir($pasta)) {
                mkdir($pasta, 0775, true);
            }

            $nomeImagem = bin2hex(random_bytes(16)) . '.' . $extensao;

            $destino = $pasta . $nomeImagem;

            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                throw new Exception("Erro ao salvar a imagem.");
            }

            $imagem = 'imagens/' . $nomeImagem;
        }

        // Atualiza os dados no banco.
        $sql = "
            UPDATE usuarios
            SET nome = ?,
                telefone = ?,
                email = ?,
                senha = ?,
                imagem = ?
            WHERE email = ?
        ";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ssssss",
            $nome,
            $telefone,
            $emailN,
            $senha,
            $imagem,
            $emailOriginal
        );

        $stmt->execute();
        $stmt->close();

        // Atualiza a sessão caso o e-mail tenha sido alterado.
        $_SESSION['email'] = $emailN;

        $emailOriginal = $emailN;

        // Busca novamente os dados atualizados.
        $usuario = buscarUsuario($conexao, $emailOriginal);

        $salvou = true;

    } catch (mysqli_sql_exception $e) {

        if ($e->getCode() === 1062) {
            $erro = "Este e-mail já está em uso por outro usuário.";
        } else {
            $erro = "Ocorreu um problema ao atualizar os dados. Tente novamente.";
        }

    } catch (Exception $e) {

        $erro = $e->getMessage();

    }
}

// Tipo do usuário para controlar os links administrativos.
$tipo = $usuario['tipo'] ?? 'cliente';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Alterar Perfil - Chingling</title>

    <link rel="stylesheet" href="style2.css">
</head>

<body>

    <!-- BARRA SUPERIOR -->
    <div id="topo-beneficios">

        <div class="topo-esquerda">
            <span>🚚 Entrega para todo o Brasil</span>
            <span class="separador-topo">|</span>
            <span>✔️ Compra segura</span>
            <span class="separador-topo">|</span>
            <span>⭐ Qualidade e conforto para o seu lar</span>
        </div>

        <div class="topo-direita">

            <span>Olá, <?= escapar($usuario['nome']) ?>!</span>

            <form action="Login.php" method="POST">
                <input type="hidden" name="sinal_clique" value="ativado">
                <button type="submit" class="botao-sair">Sair da conta</button>
            </form>

        </div>

    </div>

    <!-- BARRA PRINCIPAL -->
    <div id="barra-principal">

        <a href="Painel.php" class="logo">
            <span>🏠 Chingling</span>
            <small>Móveis para todos os momentos</small>
        </a>

        <form method="GET" action="Painel.php" class="busca busca-painel-topo">

            <input
                type="text"
                name="busca_nome"
                placeholder="Olá, o que você procura hoje?"
                aria-label="Pesquisar móveis"
            >

            <button type="submit" aria-label="Pesquisar">🔍</button>

        </form>

        <div class="atalho-saldo-topo">
            <span>💳</span>

            <div>
                <strong>Meu saldo</strong>
                <small>
                    R$ <?= number_format((float)$usuario['saldo'], 2, ',', '.') ?>
                </small>
            </div>
        </div>

    </div>

    <!-- MENU -->
    <nav id="menu-categorias">

        <a href="Painel.php">Início</a>

        <?php if ($tipo === 'admin'): ?>

            <a href="AdmMoveis.php">Administração de Móveis</a>
            <a href="AdmUsuarios.php">Administração de Usuários</a>
            <a href="AdmCadastroMoveis.php">Cadastro de Móveis</a>

        <?php endif; ?>

    </nav>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="perfil-container">

        <section class="perfil-introducao">

            <h1>Minha conta</h1>

            <p>
                Gerencie suas informações pessoais e mantenha
                seu perfil atualizado no Chingling.
            </p>

        </section>

        <section class="perfil-caixa">

            <div class="perfil-cabecalho">

                <span class="perfil-icone-cabecalho">⚙️</span>

                <div>
                    <h2>Alterar dados do perfil</h2>

                    <p>
                        Atualize suas informações pessoais abaixo.
                    </p>
                </div>

            </div>

            <?php if ($salvou): ?>

                <div class="perfil-mensagem sucesso">
                    <strong>✓ Dados atualizados com sucesso!</strong>
                    <span>Suas informações foram salvas.</span>
                </div>

            <?php endif; ?>

            <?php if (!empty($erro)): ?>

                <div class="perfil-mensagem erro">
                    <?= escapar($erro) ?>
                </div>

            <?php endif; ?>

            <form
                method="POST"
                enctype="multipart/form-data"
                class="perfil-formulario"
            >

                <!-- FOTO DE PERFIL -->
                <div class="perfil-foto-area">

                    <div class="perfil-foto-circulo">

                        <?php if (!empty($usuario['imagem'])): ?>

                            <img
                                src="<?= escapar($usuario['imagem']) ?>"
                                alt="Foto de perfil"
                                id="preview-foto-perfil"
                                class="perfil-foto-imagem"
                            >

                        <?php else: ?>

                            <div
                                id="preview-foto-padrao"
                                class="perfil-foto-padrao"
                            >
                                👤
                            </div>

                            <img
                                src=""
                                alt="Prévia da foto de perfil"
                                id="preview-foto-perfil"
                                class="perfil-foto-imagem"
                                style="display: none;"
                            >

                        <?php endif; ?>

                    </div>

                    <h3><?= escapar($usuario['nome']) ?></h3>

                    <p>Foto de perfil</p>

                    <label for="imagem" class="perfil-botao-imagem">
                        Escolher nova foto
                    </label>

                    <input
                        type="file"
                        id="imagem"
                        name="imagem"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                    >

                    <small>
                        JPG, PNG, GIF ou WEBP. Máximo de 5 MB.
                    </small>

                </div>

                <div class="perfil-divisor"></div>

                <!-- CAMPOS -->
                <div class="perfil-campos">

                    <div class="perfil-campo">
                        <label for="nome">Nome completo</label>

                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            value="<?= escapar($usuario['nome']) ?>"
                        >
                    </div>

                    <div class="perfil-campo">
                        <label for="telefone">Telefone</label>

                        <input
                            type="text"
                            id="telefone"
                            name="telefone"
                            value="<?= escapar($usuario['telefone']) ?>"
                        >
                    </div>

                    <div class="perfil-campo perfil-campo-completo">
                        <label for="emailN">E-mail</label>

                        <input
                            type="email"
                            id="emailN"
                            name="emailN"
                            value="<?= escapar($usuario['email']) ?>"
                        >
                    </div>

                    <div class="perfil-campo perfil-campo-completo">
                        <label for="senha">
                            Nova senha
                            <small>(Deixe em branco para manter a atual)</small>
                        </label>

                        <input
                            type="password"
                            id="senha"
                            name="senha"
                            placeholder="Digite uma nova senha"
                        >
                    </div>

                </div>

                <button type="submit" class="perfil-botao-salvar">
                    Salvar alterações
                </button>

            </form>

            <div class="perfil-rodape">
                <a href="Painel.php">← Voltar para o início</a>
            </div>

        </section>

    </main>

    <script>
        const campoImagem = document.getElementById('imagem');
        const previewImagem = document.getElementById('preview-foto-perfil');
        const fotoPadrao = document.getElementById('preview-foto-padrao');

        campoImagem.addEventListener('change', function () {

            const arquivo = this.files[0];

            if (!arquivo) {
                return;
            }

            if (!arquivo.type.startsWith('image/')) {
                alert('Selecione um arquivo de imagem válido.');
                this.value = '';
                return;
            }

            const leitor = new FileReader();

            leitor.onload = function (evento) {

                previewImagem.src = evento.target.result;
                previewImagem.style.display = 'block';

                if (fotoPadrao) {
                    fotoPadrao.style.display = 'none';
                }

            };

            leitor.readAsDataURL(arquivo);

        });
    </script>

</body>
</html>
