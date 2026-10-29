<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Document</title>

    <style>


.pagina {
    display: grid;
    grid-template-rows: 1fr 3fr;
    gap: 20px;
} 
    
/*
.pagina {
    position: fixed;
    inset: 0;
    border: 10px solid blue;
    box-sizing: border-box;
    z-index: 9999;
    pointer-events: none;
}
    */

body::before {
    content: "";
    position: fixed;
    inset: 0;
    border: 10px solid blue;
    box-sizing: border-box;
    z-index: 999999;
    pointer-events: none;
}
/*
.conteudo{

border: 10px solid blue;
    box-sizing: border-box;
    z-index: 999999;
    pointer-events: none;

display: grid;
    grid-template-columns: 3fr 1fr;
    gap: 20px;

}

.lado{

border: 10px solid blue;
    box-sizing: border-box;
    z-index: 999999;
    pointer-events: none;

 display: grid;
    grid-template-rows: 1fr 1fr 1fr;
    gap: 20px;

}

*/

.topo{

background-color: aqua;

   position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px;

        display: flex;
        align-items: center;

         height: 85px;
}

.topo2{

display: flex;
    gap: 20px;

}

 h1 { 
    
 margin-left: 20px;

height: 300px;
        display: flex;
        align-items: center;

    }

    h2{

            position: absolute;
        left: 50%;
        transform: translateX(-50%);

    }

    .foto_de_perfil{

 margin-left: 30px;

     position: absolute;
        left: 90%;
        transform: translateX(-50%);

        width: 100px;
    height: 60px;
    }

  /*  .esquerda div,
.direita {
    background-color: black;
}

*/

    </style>

</head>
<body>
   
<div class="pagina">

<div class="topo">

<div class="topo2">

<h1>Seu perfil</h1>


<h2>Chingling Shop</h2>

<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSUsY32NgzN46CXhGcXkBOwQ52EUkpAiqigEbxRs4wz_21sOKkrBq6gTsY&s=10" alt="
" class="foto_de_perfil">


</div>


</div>

<div class="conteudo">

<div></div>
<div></div>
<div></div>

</div>

<div class="lado"></div>

 </div>
    
</body>
</html>