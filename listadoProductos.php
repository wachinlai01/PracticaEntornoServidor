<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de productos</title>
    <?php require "conexionTienda.php" ?>
    <?php require "classProducto.php" ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<body>
    <?php
        session_start();
        if (isset($_SESSION["usuario"])){
            $usuario=$_SESSION["usuario"];
            //Obtenemos el resultado de la usuario
            $consulta = "SELECT rol FROM usuarios WHERE usuario='$usuario'";
            $resultado = $conexion->query($consulta);
            $fila = $resultado->fetch_assoc();
            $rol = $fila['rol'];
        }else{
            $usuario="invitado";
        }

        //Vamos a crear los objeto productos
        $sql = "SELECT * FROM productos ";
        $resultado = $conexion -> query($sql);
        $productos=[];
        while($fila=$resultado -> fetch_assoc()){
            $idProducto = $fila ["idProducto"];
            $nombreProducto = $fila ["nombreProducto"];
            $precio = $fila ["precio"];
            $descripcion = $fila ["descripcion"];
            $cantidad = $fila ["cantidad"];
            $imagen = $fila ["imagen"];
            $nuevoProducto=new Producto($idProducto,$nombreProducto,$precio,$descripcion,
                                            $cantidad,$imagen);
            array_push($productos,$nuevoProducto);
        }
    ?>

    <?php
    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $id_producto=$_POST["id_producto"];
        //echo "<p>El videojuego selecionado es $id_producto</p>";

        $consultaCesta="SELECT idCesta FROM cestas WHERE usuario='$usuario'";
        $resultadoCesta= $conexion->query($consultaCesta);
        $filaCesta=$resultadoCesta->fetch_assoc();
        $idCesta=$filaCesta["idCesta"];
        echo $idCesta;
    }
    ?>
    <div class="container">
        <h1 style="text-align:center; margin:20px;">Listado Productos</h1>
        <p style="text-align:center;">Bienvenido <?php echo $usuario?> </p>
        <?php
        if (isset($_SESSION["usuario"])){?>
            <button class="btn btn-dark" style= "float:right; margin:10px">
                <a href="cerrarsesion.php" style="text-decoration:none; color:white">Cerrar sesión</a>
            </button><?php
        }else{?>
            <button class="btn btn-dark" style= "float:right; margin:10px">
                <a href="inicio_sesion.php" style="text-decoration:none; color:white">Iniciar sesión</a>
            </button><?php
        }
        if (isset($rol)){
            if ($rol=="admin"){?>
                <button class="btn btn-dark" style= "float:right; margin:10px">
                    <a href="registroProductos.php" style="text-decoration:none; color:white">Agregar Producto</a>
                </button><?php
            }
        }
        ?>
        <table class="table table-primary">
            <thead class="table-dark">
                <tr>
                    <th>id</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Imagen</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($productos as $producto){?>
                    <tr>
                        <td><?php echo $producto->idProducto ?></td>
                        <td><?php echo $producto->nombreProducto ?></td>
                        <td><?php echo $producto->precio ?></td>
                        <td><?php echo $producto->descripcion ?></td>
                        <td><?php echo $producto->cantidad ?></td>
                        <td>
                        <img  height="80" src="<?php echo $producto->imagen?>">
                        </td>
                        <td>
                        <form action="" method="post">
                            <input type="hidden" name="id_producto" value="<?php echo $producto->idProducto?>">
                            <input class="btn btn-warning" type="submit" value="Añadir cesta">
                        </form>
                        </td>
                        <?php
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>