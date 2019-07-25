<?php
include_once("./conx.php");
include_once("./includes/Parsedown.php");

$parser = new Parsedown();
$error = false;

if(!isset($_GET["idtrabajo"])) $error = "<h1>¡Ups! Algo ha fallado... <br> (No se pudo encontrar el trabajo)</h1>";
//Escapa los caracteres especiales de la variable 
else $idtrabajo = mysqli_real_escape_string($link, $_GET["idtrabajo"]);

//El "@" suprime los errores y alertas
$archivo = @file_get_contents("./files/$idtrabajo.md");
//Verifica si hay contenido en el archivo
if(!$archivo && !strlen($archivo)){
    $error = "<h1>¡Ups! Algo ha fallado... <br> (No hay archivo que abrir)</h1>";
}

if(!$error){
    $query = "SELECT * FROM trabajos WHERE idtrabajo = $idtrabajo";
    $trabajo = mysqli_query($link, $query);
    if(mysqli_num_rows($trabajo) == 1){
        $trabajo = mysqli_fetch_array($trabajo);
        $materia = mysqli_fetch_array(mysqli_query($link, "SELECT nom FROM materias WHERE idmateria = $trabajo[1]"));
    }else $error = "<h1>¡Ups! Algo ha fallado... <br> (Error en la Base de Datos)</h1>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/prism.css">
    <title><?php echo (!$error ? $trabajo[2] : "¡Ups!"); ?> - Lisandro Marchionni</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="/marchionni">Lisandro Marchionni</a></li>
        </ul>
    </nav>
    <div class="container row">
        <aside class="col-2 sidebar">
            <div class="anclas-container">
                <h3>Tabla de contenido</h3>
                <ul id="contenido" style="margin: 10px 0 0 20px;"><?php echo (!$error ? "Cargando contenido..." : "¡No hay nada que ver aquí!"); ?></ul>
            </div>
        </aside>
        <div class="col-10 col-push-2 md-container">
            <?php

            if($error) echo $error;
            //Convierto el contenido de Markdown a HTML
            else {
                echo '<h1><?php echo $trabajo[2]; ?></h1> <br>';
                echo $parser->text($archivo);
            }
            ?>
        </div>
    </div>
    <script src="js/prism.js"></script>
    <script>
    window.addEventListener("hashchange", function() { scrollBy(0, -60) });
    
    window.onload = cargarContenido = () => {
        let content = document.querySelector("#contenido");
        let anchors = document.querySelectorAll(".anchor");
        
        if(anchors.length > 1) content.innerHTML='';

        //elementoAnterior = [tipo, elementoPadre]
        let ant = [null, content];
        anchors.forEach(el => {
            //Que tipo de titulo es
            let type = el.tagName[1];
            //Creo el elemento li
            let li = document.createElement('li');
            li.innerHTML = `<a href="#${el.id}">${el.innerHTML}</a>`;

            //Si no hay elemento anterior, o es del mismo tipo
            if(ant[0] == null || ant[0] == type) ant[1].appendChild(li);
            //Si el tipo es menor (o sea, es un subtitulo)
            else if(ant[0] < type) {
                ant[1] = ant[1].appendChild(document.createElement('ul'));
                ant[1].appendChild(li);
            }
            //Si el tipo es mayor (sube una jerarquía)
            else if (ant[0] > type){
                do {
                    ant[1] = ant[1].parentElement;
                    ant[0]--;
                } while(ant[0] > type);
                ant[1].appendChild(li);
            }

            console.log(type);
            ant[0] = type;
        })
    } 
    </script>
</body>
</html>