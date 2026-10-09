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

require_once 'conexao_msql.php';

$sql = "INSERT INTO produtos (nome, estoque, preco, categoria_id, imagem, descricao)
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

// Mensagem que será enviada para a outra página
$_SESSION['mensagem'] = "Móvel cadastrado com sucesso!";

header("Location: AdmMoveis.php");
exit();

}
    }

?>