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
            //Obtenemos rol del usuario
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

        if ($_SERVER["REQUEST_METHOD"]=="POST"){
            $id_producto=$_POST["id_producto"];
            $unidades_tmp=$_POST["unidades"];
            //Array con los valores validos del select
            $valoresPermitidos = ['1', '2', '3','4','5'];
            //Obtenemos el id de la cesta
            $consultaCesta="SELECT idCesta FROM cestas WHERE usuario='$usuario'";
            $resultadoCesta= $conexion->query($consultaCesta);
            $filaCesta=$resultadoCesta->fetch_assoc();
            $idCesta=$filaCesta["idCesta"];
            //Almacenamos el id de la cesta para usarla en otra página
            $_SESSION['idCesta'] = $idCesta;

            if (isset($unidades_tmp) && in_array($unidades_tmp, $valoresPermitidos)) {
                $unidades=$unidades_tmp;
            }else{
                $error_unidades="No intentes hackearme";
            }
            //Almacenamos el numero de unidades seleccionadas para usarla en otra página
            $_SESSION['unidades']=$unidades;

            echo $_SESSION['unidades'];
            echo $_SESSION['idCesta'];
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
                    <th>Imagen</th><?php
                    if ($usuario!="invitado"){?>
                        <th></th><?php
                    }?>
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
                        </td><?php
                        if ($usuario!="invitado"){?>
                            <td>
                                <form action="" method="post">
                                    <input type="hidden" name="id_producto" value="<?php echo $producto->idProducto?>">
                                    <select name="unidades"><?php
                                        if (isset($error_unidades)){
                                            echo $error_unidades;
                                        }
                                        ?>
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    <input class="btn btn-warning" type="submit" value="Añadir cesta">
                                </form>
                            </td><?php
                        }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    if(isset($unidades)) {
        echo "<h3>Id del producto: $id_producto</h3>";
        echo "<h3>Id de la cesta: $idCesta</h3>";
        echo "<h3>Cantidad a comprar: $unidades</h3>";
        $sql = "INSERT INTO productosCestas (idProducto, idCesta, cantidad)
            VALUES ('$id_producto','$idCesta','$unidades')";
        $conexion->query($sql);
    }
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>