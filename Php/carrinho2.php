
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chingling - Carrinho de Compras</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7fa;
            color: #26364b;
        }

        header {
            background: #0863c9;
            color: white;
        }

        .topbar {
            max-width: 1200px;
            margin: auto;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .nav {
            background: white;
            color: #0863c9;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            font-size: 27px;
            font-weight: bold;
        }

        .search {
            width: 45%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
        }

        .menu {
            background: #0756ad;
            padding: 13px 20px;
            display: flex;
            justify-content: center;
            gap: 35px;
            font-size: 13px;
        }

        .container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .title .icon {
            font-size: 38px;
        }

        .title h1 {
            color: #075bb8;
            font-size: 26px;
        }

        .title p {
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }

        .content {
            display: grid;
            grid-template-columns: 1.7fr 1fr;
            gap: 22px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e6ebf1;
            margin-bottom: 18px;
        }

        .card h3 {
            color: #075bb8;
            font-size: 16px;
            margin-bottom: 18px;
        }

        .product-header,
        .product {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 20px;
            align-items: center;
            gap: 10px;
        }

        .product-header {
            font-size: 12px;
            font-weight: bold;
            color: #63758b;
            padding-bottom: 15px;
        }

        .product {
            border-top: 1px solid #edf0f4;
            padding: 15px 0;
            font-size: 13px;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-image {
            width: 85px;
            height: 80px;
            object-fit: cover;
            border-radius: 7px;
            background: #eee;
        }

        .product-info strong {
            display: block;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .product-info small {
            color: #8491a2;
        }

        .quantity {
            display: flex;
            align-items: center;
            border: 1px solid #e1e7ee;
            border-radius: 5px;
            width: fit-content;
        }

        .quantity button {
            background: white;
            border: none;
            color: #0863c9;
            padding: 7px 10px;
            cursor: pointer;
        }

        .quantity span {
            min-width: 22px;
            text-align: center;
        }

        .price {
            color: #075bb8;
            font-weight: bold;
        }

        .remove {
            color: #aab2bc;
            border: none;
            background: transparent;
            font-size: 18px;
            cursor: pointer;
        }

        .tools {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .tools .card {
            margin: 0;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #dce3eb;
            border-radius: 5px;
            margin-bottom: 10px;
            outline: none;
        }

        input:focus {
            border-color: #0863c9;
        }

        .primary {
            background: #0863c9;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 11px 17px;
            cursor: pointer;
            font-weight: bold;
        }

        .tools .primary {
            width: 100%;
        }

        .summary {
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
            font-size: 14px;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            color: #075bb8;
            font-weight: bold;
            font-size: 21px;
            padding: 20px 0;
        }

        .checkout {
            width: 100%;
            padding: 15px;
            font-size: 15px;
        }

        .security {
            text-align: center;
            font-size: 12px;
            color: #8995a5;
            margin-top: 15px;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #8995a5;
            font-size: 12px;
        }

        @media (max-width: 750px) {
            .content {
                grid-template-columns: 1fr;
            }

            .nav {
                flex-wrap: wrap;
            }

            .search {
                width: 100%;
            }

            .menu {
                justify-content: flex-start;
                overflow-x: auto;
                white-space: nowrap;
            }

            .product-header {
                display: none;
            }

            .product {
                grid-template-columns: 1fr auto;
                row-gap: 12px;
            }

            .product-info {
                grid-column: 1 / -1;
            }

            .tools {
                grid-template-columns: 1fr;
            }

            .summary {
                position: static;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="topbar">
            <span>🚚 Chingling Online</span>
            <span>♙ Minha conta &nbsp;&nbsp; 🛒 Carrinho (3)</span>
        </div>

        <div class="nav">
            <div class="logo">🏠 Chingling</div>

            <input
                type="text"
                class="search"
                placeholder="O que você procura hoje?"
            >

            <strong>📍 Informe seu CEP</strong>
        </div>

        <nav class="menu">
            <span>Sala de Estar</span>
            <span>Quartos</span>
            <span>Sala de Jantar</span>
            <span>Cozinha</span>
            <span>Escritório</span>
            <span>Banheiro</span>
            <span>Promoções</span>
        </nav>
    </header>

    <main class="container">

        <div class="title">
            <span class="icon">🛒</span>

            <div>
                <h1>Carrinho de compras</h1>
                <p>Revise os itens do seu carrinho antes de finalizar.</p>
            </div>
        </div>

        <div class="content">

            <section>

                <div class="card">

                    <div class="product-header">
                        <span>Produto</span>
                        <span>Quantidade</span>
                        <span>Preço</span>
                        <span>Subtotal</span>
                        <span></span>
                    </div>

                    <div class="product">

                        <div class="product-info">
                            <img class="product-image"
                                 src="https://placehold.co/100x90/e8e2d9/555?text=Sofa"
                                 alt="Sofá">

                            <div>
                                <strong>Sofá Retrátil e Reclinável 3 Lugares Tecido Linho</strong>
                                <small>Cor: Bege</small>
                            </div>
                        </div>

                        <div class="quantity">
                            <button>-</button>
                            <span>1</span>
                            <button>+</button>
                        </div>

                        <strong class="price">R$ 1.899,00</strong>
                        <strong class="price">R$ 1.899,00</strong>

                        <button class="remove">×</button>

                    </div>

                    <div class="product">

                        <div class="product-info">
                            <img class="product-image"
                                 src="https://placehold.co/100x90/e8e2d9/555?text=Rack"
                                 alt="Rack">

                            <div>
                                <strong>Rack para TV até 65 Polegadas</strong>
                                <small>Cor: Madeira/Off White</small>
                            </div>
                        </div>

                        <div class="quantity">
                            <button>-</button>
                            <span>1</span>
                            <button>+</button>
                        </div>

                        <strong class="price">R$ 799,00</strong>
                        <strong class="price">R$ 799,00</strong>

                        <button class="remove">×</button>

                    </div>

                    <div class="product">

                        <div class="product-info">
                            <img class="product-image"
                                 src="https://placehold.co/100x90/e8e2d9/555?text=Poltrona"
                                 alt="Poltrona">

                            <div>
                                <strong>Poltrona Decorativa Nina</strong>
                                <small>Cor: Cinza</small>
                            </div>
                        </div>

                        <div class="quantity">
                            <button>-</button>
                            <span>1</span>
                            <button>+</button>
                        </div>

                        <strong class="price">R$ 599,00</strong>
                        <strong class="price">R$ 599,00</strong>

                        <button class="remove">×</button>

                    </div>

                </div>

                <div class="tools">

                    <div class="card">
                        <h3>🏷️ Cupom de desconto</h3>

                        <input
                            type="text"
                            placeholder="Digite seu cupom"
                        >

                        <button class="primary">Aplicar</button>
                    </div>

                    <div class="card">
                        <h3>🚚 Calcular frete</h3>

                        <input
                            type="text"
                            placeholder="Digite seu CEP"
                        >

                        <button class="primary">Calcular</button>
                    </div>

                </div>

            </section>

            <aside class="card summary">

                <h3>📋 Resumo do pedido</h3>

                <div class="summary-line">
                    <span>Subtotal (3 itens)</span>
                    <strong>R$ 3.297,00</strong>
                </div>

                <div class="summary-line">
                    <span>Desconto</span>
                    <strong>R$ 0,00</strong>
                </div>

                <div class="summary-line">
                    <span>Frete</span>
                    <strong>A calcular</strong>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>R$ 3.297,00</span>
                </div>

                <button class="primary checkout">
                    Finalizar compra →
                </button>

                <p class="security">
                    🔒 Compra segura e protegida
                </p>

            </aside>

        </div>

    </main>

    <footer>
        © 2026 Chingling Online - Todos os direitos reservados.
    </footer>

</body>
</html>
