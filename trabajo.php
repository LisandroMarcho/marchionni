<?php
include_once("./conx.php");
include_once("./includes/Parsedown.php");

$parse = new Parsedown();
$error = false;

if(!isset($_GET["idtrabajo"])) $error = "¡Ups! Algo ha fallado... <br> (No se pudo encontrar el trabajo)";
//Escapa los caracteres especiales de la variable 
else $idtrabajo = mysqli_real_escape_string($link, $_GET["idtrabajo"]);

//El "@" suprime los errores y alertas
$archivo = @file_get_contents("./files/$idtrabajo.md");
//Verifica si hay contenido en el archivo
if(!$archivo && !strlen($archivo)){
    $error = "¡Ups! Algo ha fallado... <br> (No hay archivo que abrir)";
}

if(!$error){
    $query = "SELECT * FROM trabajos WHERE idtrabajo = $idtrabajo";
    $trabajo = mysqli_query($link, $query);
    if(mysqli_num_rows($trabajo) == 1){
        $trabajo = mysqli_fetch_array($trabajo);
        $materia = mysqli_fetch_array(mysqli_query($link, "SELECT nom FROM materias WHERE idmateria = $trabajo[1]"));
    }else $error = "¡Ups! Algo ha fallado... <br> (Error en la Base de Datos)";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/main.css">
    <title><?php echo (!$error ? $trabajo[2] : "¡Ups!"); ?> - Lisandro Marchionni</title>
</head>
<body>
    <nav>

    </nav>
    <div class="md">
        <?php
        if($error) echo $error;
        else{
            //Convierto el contenido de Markdown a HTML
            echo $parse->text($archivo);
        }
        ?>
    </div>
</body>
</html>