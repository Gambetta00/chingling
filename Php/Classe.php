
<?php

Class Usuario {

    private $nome;
    private $email;
    private $senha;
    private $cpf;
    private $telefone;
    private $classe;
    private $conexao;

    public function __construct($nome, $email, $senha, $cpf, $telefone, $classe, $conexao) {
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->cpf = $cpf;
        $this->telefone = $telefone;
        $this->classe = $classe;
        $this->conexao = $conexao;
    }

    
public function Set_Movel($nome, $estoque, $preco, $categoria, $imagem, $descricao, $conexao){

    // Verificar se o móvel já existe.
    $sql = "SELECT id FROM produtos
            WHERE nome = ?
            AND categoria_id = ?";

    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        $_SESSION['mensagem'] = "Erro ao verificar o móvel.";
        return false;
    }

    $stmt->bind_param("si", $nome, $categoria);
    $stmt->execute();
    $stmt->store_result();

    // Se encontrou um registro igual.
    if ($stmt->num_rows > 0) {

        $_SESSION['mensagem'] = "Este móvel já está cadastrado!";

        $stmt->close();

        return false;
    }

    $stmt->close();

    // Cadastrar o móvel.
    $sql = "INSERT INTO produtos
            (nome, estoque, preco, categoria_id, imagem, descricao)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        $_SESSION['mensagem'] = "Erro ao preparar o cadastro.";
        return false;
    }

    $stmt->bind_param(
        "sidi ss",
        $nome,
        $estoque,
        $preco,
        $categoria,
        $imagem,
        $descricao
    );

    // Executar o cadastro.
    if (!$stmt->execute()) {
        $_SESSION['mensagem'] = "Erro ao cadastrar o móvel.";
        $stmt->close();
        return false;
    }

    $stmt->close();

    $_SESSION['mensagem'] = "Móvel cadastrado com sucesso!";

    return true;
}


    public function User_Login($email, $senha, $conexao) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sql = "SELECT email, senha, tipo FROM usuarios WHERE email = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {

            $stmt->bind_result($emailBanco, $hash, $tipoBanco);
            $stmt->fetch();

            if (password_verify($senha, $hash)) {

                // Salva as informações do usuário na sessão
                $_SESSION['email'] = $emailBanco;
                $_SESSION['tipo'] = $tipoBanco;

                $stmt->close();

                header("Location: Painel.php");
                exit;

            } else {

                $_SESSION['mensagem'] = "Senha incorreta.";

            }

        } else {

            $_SESSION['mensagem'] = "Email não encontrado.";

        }

        $stmt->close();

        header("Location: Login.php");
        exit;
    }
}

?>
