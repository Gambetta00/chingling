<?php
 Class Movel{
    protected $nome;
    protected $estoque;
    protected $preco;
    protected $categoria;
    protected $imagem;
    protected $descricao;
    protected $conexao;

public function __construct($nome, $estoque, $preco, $categoria, $imagem, $descricao, $conexao){
        $this->nome = $nome;
        $this->estoque = $estoque;
        $this->preco = $preco;
        $this->categoria = $categoria;
        $this->imagem = $imagem;
        $this->descricao = $descricao;
        $this->conexao = $conexao;
    }

public function Set_Movel($nome, $estoque, $preco, $categoria, $imagem, $descricao, $conexao){

    // Verificar se o móvel já existe
    $sql = "SELECT nome FROM produtos
            WHERE nome = ?
            AND categoria_id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ss",
        $nome,
        $categoria,
    );

    $stmt->execute();
    $stmt->store_result();

    // Se encontrou um registro igual
    if ($stmt->num_rows > 0) {

        $_SESSION['mensagem'] = "Este móvel já está cadastrado!";

        $stmt->close();

        header("Location: AdmMoveis.php");
        exit();

    }

    $stmt->close();

    // Se não existe, cadastrar
    $sql = "INSERT INTO produtos
            (nome, estoque, preco, categoria_id, imagem, descricao)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ssssss",
        $nome,
        $estoque,
        $preco,
        $categoria,
        $imagem,
        $descricao
    );

    $stmt->execute();
    $stmt->close();

    $_SESSION['mensagem'] = "Móvel cadastrado com sucesso!";

    header("Location: AdmMoveis.php");
    exit();
}

public function Edit_Movel($nome, $estoque, $preco, $categoria, $imagem, $descricao, $conexao){

    $sql = "UPDATE produtos
            SET estoque = ?,
                preco = ?,
                imagem = ?,
                descricao = ?
            WHERE nome = ?
            AND categoria_id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sdssss",
        $estoque,
        $preco,
        $imagem,
        $descricao,
        $nome,
        $categoria
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['mensagem'] = "Móvel editado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Nenhum móvel foi alterado.";
    }

    $stmt->close();

    header("Location: AdmMoveis.php");
    exit();
}

public function UnSet_Movel($nome, $categoria, $conexao){

    // Verificar se o móvel existe
    $sql = "SELECT nome FROM produtos
            WHERE nome = ?
            AND categoria_id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ss",
        $nome,
        $categoria,
    );

    $stmt->execute();
    $stmt->store_result();

    // Se encontrou um registro, deleta
    if ($stmt->num_rows > 0) {

        $sql = "DELETE FROM produtos 
                WHERE nome = ?
                AND categoria_id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ss",
            $nome,
            $categoria,
        );

        $stmt->execute();
        $stmt->store_result();

        $_SESSION['mensagem'] = "Movel deletado com sucesso!";

        $stmt->close();

        header("Location: AdmMoveis.php");
        exit();

    }else{

        $_SESSION['mensagem'] = "Movel não encontrado!";

        $stmt->close();

        header("Location: AdmMoveis.php");
        exit();

    }
}

    }

?>