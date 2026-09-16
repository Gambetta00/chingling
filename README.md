[chingling.txt](https://github.com/user-attachments/files/32070194/chingling.txt)

CREATE DATABASE chingling;
USE chingling;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') DEFAULT 'cliente',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    imagem VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE SET NULL
);


CREATE TABLE produtos(
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    status ENUM(
        'pendente',
        'pago',
        'enviado',
        'entregue',
        'cancelado'
    ) DEFAULT 'pendente',

    total DECIMAL(10,2) NOT NULL DEFAULT 0,

    FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON DELETE CASCADE,

    FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
        ON DELETE CASCADE
);


CREATE TABLE enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    rua VARCHAR(150) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    cep VARCHAR(10) NOT NULL,

    FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

INSERT INTO categorias (nome, descricao) VALUES
('Eletrônicos', 'Produtos eletrônicos e tecnológicos'),
('Informática', 'Computadores e acessórios'),
('Periféricos', 'Mouse, teclado, headset e outros'),
('Celulares', 'Smartphones e acessórios'),
('Games', 'Produtos para gamers');

INSERT INTO produtos
(categoria_id, nome, descricao, preco, estoque, imagem)
VALUES

neste espaço abaixo botar os produtos um por um que foram adicionados ao site 




















INSERT INTO usuarios (nome, email, senha, tipo)
VALUES
('Administrador', 'admin@chingling.com', '123456', 'admin');


INSERT INTO usuarios (nome, email, senha, tipo)
VALUES
('Cliente Teste', 'cliente@chingling.com', '123456', 'cliente');
