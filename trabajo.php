<?php
include_once("./conx.php");
include_once("./includes/Parsedown.php");

$parser = new Parsedown();
$error = false;

if(!isset($_GET["idtrabajo"])) $error = "<h2>¡Ups! Algo ha fallado... <br> (No se pudo encontrar el trabajo)</h2>";
//Escapa los caracteres especiales de la variable 
else $idtrabajo = mysqli_real_escape_string($link, $_GET["idtrabajo"]);

//El "@" suprime los errores y alertas
$archivo = @file_get_contents("./files/$idtrabajo.md");
//Verifica si hay contenido en el archivo
if(!$archivo && !strlen($archivo)){
    $error = "<h2>¡Ups! Algo ha fallado... <br> (No hay archivo que abrir)</h2>";
}

if(!$error){
    $query = "SELECT * FROM trabajos WHERE idtrabajo = $idtrabajo";
    $trabajo = mysqli_query($link, $query);
    if(mysqli_num_rows($trabajo) == 1){
        $trabajo = mysqli_fetch_array($trabajo);
        $materia = mysqli_fetch_array(mysqli_query($link, "SELECT nom FROM materias WHERE idmateria = $trabajo[1]"));
    }else $error = "<h2>¡Ups! Algo ha fallado... <br> (Error en la Base de Datos)</h2>";
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
    <!-- Si, no hay errores, imprime el nombre del trabajo. De lo contrario imprime Ups -->
    <title><?php echo (!$error ? $trabajo[2] : "¡Ups!"); ?> - Lisandro Marchionni</title>
</head>
<body>
    <header>
        <a class="logo" href="/marchionni">Lisandro Marchionni</a>
    </header>
    <div class="container row">
        <aside class="col-2 sidebar">
            <div class="anclas-container">
                <h3 class="content-header">Tabla de contenido</h3>
                <ul id="contenido" style="margin: 10px 0 0 20px;"><?php echo (!$error ? "Cargando contenido..." : "¡No hay nada que ver aquí!"); ?></ul>
            </div>
        </aside>
        <div class="col-10 col-push-2 md-container">
            <div class="sm-padding">
                <?php

                if($error) echo $error;
                //Convierto el contenido de Markdown a HTML
                else {
                    echo "<h1>$trabajo[2]</h1> <br>";
                    echo $parser->text($archivo);
                }
                ?>
            </div>
        </div>
        <div class="btn-up"><svg onclick="window.location = '#'" class="svg-icon" viewBox="0 0 20 20">
							<path d="M13.889,11.611c-0.17,0.17-0.443,0.17-0.612,0l-3.189-3.187l-3.363,3.36c-0.171,0.171-0.441,0.171-0.612,0c-0.172-0.169-0.172-0.443,0-0.611l3.667-3.669c0.17-0.17,0.445-0.172,0.614,0l3.496,3.493C14.058,11.167,14.061,11.443,13.889,11.611 M18.25,10c0,4.558-3.693,8.25-8.25,8.25c-4.557,0-8.25-3.692-8.25-8.25c0-4.557,3.693-8.25,8.25-8.25C14.557,1.75,18.25,5.443,18.25,10 M17.383,10c0-4.07-3.312-7.382-7.383-7.382S2.618,5.93,2.618,10S5.93,17.381,10,17.381S17.383,14.07,17.383,10"></path>
						</svg></div>
    </div>
    <script src="js/prism.js"></script>
    <script>
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

            ant[0] = type;
        })
    } 
    </script>
</body>
</html>