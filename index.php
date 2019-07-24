<?php
include_once("conx.php");

$query = "SELECT * FROM trabajos WHERE anio='2019' ORDER BY idmateria ASC";
$trabajos = mysqli_query($link, $query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/main.css">
    <title>Lisandro Marchionni</title>
</head>

<body>
    <nav>

    </nav>
    <!-- Trabajos -->
    <div class="container">
    <?php
    if ($trabajos && mysqli_num_rows($trabajos) >= 1) {
        $idmateria = null; 
        $otra = -1; //Dice si pasó de una materia a otra los trabajos. Se inicializa en -1
        
        //Recorro los trabajos
        while ($r = mysqli_fetch_array($trabajos)) {
            
            //Si idmateria es null, le asigno la materia actual
            if ($idmateria == null) $idmateria = $r["idmateria"];
            //Si ya hay un ID, y es diferente al trabajo (pasamos a otra materia), reasigna
            //el id y otra es veradera
            if ($idmateria != $r["idmateria"]) {
                $idmateria = $r["idmateria"]; 
                $otra = true;
            }
            
            //Pido el nombre de la materia
            $materia = mysqli_fetch_array(mysqli_query($link, "SELECT nom FROM materias WHERE idmateria = $idmateria"))[0];
            
            //Si es otro trabajo, cerrá y abrí otro contenedor, y agregá el titulo de la materia            
            if ($otra) {
                echo "</div><div class='container'>";
                echo "<h2 style='width: 100%;'>$materia</h2>";
            }
            //Si es otra es -1 (primer trabajo) imprimí nada más que el titulo de la materia
            else if($otra == -1) echo "<h2 style='width: 100%;'>$materia</h2>";

            echo "<p class='item'>    
                      <span>$r[2]</span><br>",
                      (!$r["descr"] ? "" : "<span>$r[3]</span>"),
                 "</p>";

            //Cambio otra a false, suponiendo que la siguiente será la misma materia
            $otra = false;
        }
    }
    ?>
    </div>
    <!-- Fin trabajos -->
</body>

</html>