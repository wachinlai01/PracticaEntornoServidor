<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php require 'conexionTienda.php' ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <?php
    session_start();
    $usuario=$_SESSION["usuario"];
    if (!isset($usuario)){
        header('location: listadoProductos.php');
    }
    $idCesta = $_SESSION['idCesta'];
    //Consulta para obtener los datos de la cesta
    $consultaCesta = "SELECT productosCestas.idProducto, productos.nombreProducto, productos.imagen, productosCestas.cantidad, productos.precio
                      FROM productosCestas
                      INNER JOIN productos ON productosCestas.idProducto = productos.idProducto
                      WHERE productosCestas.idCesta = '$idCesta'";
    $resultadoCesta = $conexion->query($consultaCesta);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["vaciarCesta"])){
            // Eliminar todos los productos de la cesta
            $sqlEliminarProductos = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion->query($sqlEliminarProductos);
            header('location: cesta.php');
        }
    }
    ?>
    <div class="container">
        <h1 style="text-align:center; margin:20px;">Cesta de <?php echo $usuario?></h1>
        <?php
        if (isset($_SESSION["usuario"])){?>
            <button class="btn btn-dark" style= "float:right; margin:10px">
                <a href="cerrarsesion.php" style="text-decoration:none; color:white">Cerrar sesión</a>
            </button>
            <button class="btn btn-dark" style= "float:right; margin:10px">
                <a href="listadoProductos.php" style="text-decoration:none; color:white">Seguir comprando</a>
            </button><?php
        }?>
        <table class="table table-primary">
            <thead class="table-dark">
                <th>Producto</th>
                <th>Imagen</th>
                <th>Cantidad</th>
                <th>Precio total</th>
            </thead>
            <tbody><?php
            while ($filaCesta = $resultadoCesta->fetch_assoc()) {?>
                <tr>
                    <td><?php echo $filaCesta['nombreProducto']?></td>
                    <td><img height="80" src="<?php echo $filaCesta['imagen']?>"></td>
                    <td><?php echo $filaCesta['cantidad']?></td>
                    <td><?php echo ($filaCesta['precio']*$filaCesta['cantidad'])?></td>
                </tr><?php
            }?>
            </tbody>
        </table>
        <form method="post" style="text-align:center;">
                <button class="btn btn-danger" name="vaciarCesta" type="submit">Vaciar Cesta</button>
        </form>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>