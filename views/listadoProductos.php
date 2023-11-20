<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de productos</title>
    <?php require "../util/conexionTienda.php" ?>
    <?php require "../util/classProducto.php" ?>
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
            //Guardamos el rol para usarlo en otras variables
            $_SESSION["rol"]=$rol;
            //Obtenemos el id de la cesta
            $consultaCesta="SELECT idCesta FROM cestas WHERE usuario='$usuario'";
            $resultadoCesta= $conexion->query($consultaCesta);
            $filaCesta=$resultadoCesta->fetch_assoc();
            $idCesta=$filaCesta["idCesta"];
            //Almacenamos el id de la cesta para usarla en otra página
            $_SESSION['idCesta'] = $idCesta;
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
            if (isset($unidades_tmp) && in_array($unidades_tmp, $valoresPermitidos)) {
                $unidades=$unidades_tmp;
            }else{
                $error_unidades="No intentes hackearme";
            }

    }
    ?>
    <div class="container">
        <h1 style="text-align:center; margin:20px;">Listado Productos</h1>
        <p style="text-align:center;">Bienvenido <?php echo $usuario?> </p>
        <?php
        if (isset($_SESSION["usuario"])){?>
            <a class="btn btn-dark" href="../util/cerrarsesion.php" style= "float:right; margin:10px; text-decoration:none; color:white">
            Cerrar sesión
            </a>
            <a class="btn btn-dark" href="cesta.php" style= "float:right; margin:10px; text-decoration:none; color:white">
            Ver cesta
            </a><?php
        }else{?>
            <a class="btn btn-dark" href="inicio_sesion.php" style= "float:right; margin:10px; text-decoration:none; color:white">
            Iniciar sesión
            </a><?php
        }
        if (isset($rol)){
            if ($rol=="admin"){?>
                <a class="btn btn-dark" href="registroProductos.php" style= "float:right; margin:10px; text-decoration:none; color:white">
                Agregar Producto
                </a><?php
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
                        if ($usuario!="invitado"){
                            if($producto->cantidad>0){?>
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
                            }else{?>
                                <td>Producto agotado</td><?php
                            }
                        }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    if(isset($unidades)) {
        // Comprobamos si el producto existe en nuestra cesta
        $consultaProductoCesta = "SELECT cantidad FROM productosCestas WHERE idProducto='$id_producto' AND idCesta='$idCesta'";
        $resultadoProductoCesta = $conexion->query($consultaProductoCesta);
        //Si devuelve algo el producto ya existe en la cesta
        if ($resultadoProductoCesta->num_rows > 0) {
            // El producto ya está en la cesta, actualizamos la cantidad
            $filaProd = $resultadoProductoCesta->fetch_assoc();
            //Cantidad existente en la cesta
            $cantidadProd = $filaProd["cantidad"];
            //La nueva cantidad, será la existente + las añadidas.
            $nuevaCantidad = $cantidadProd + $unidades;
            // Modificamos la cantidad en productos
            $consultaProducto = "SELECT cantidad FROM productos WHERE idProducto='$id_producto'";
            $resultadoProducto = $conexion->query($consultaProducto);
            $filaProd = $resultadoProducto->fetch_assoc();
            //Cantidad en stock
            $cantidadOriginal = $filaProd["cantidad"];
            //Valor tras eliminar stock
            $nuevaCantidadProductos = $cantidadOriginal - $unidades;
            if ($nuevaCantidadProductos > 0){
                //Si es positivo actualizo al nuevo valor
                $sql = "UPDATE productos SET cantidad='$nuevaCantidadProductos' WHERE idProducto='$id_producto'";
                $conexion->query($sql);
            }else{
                //Si es negativo o 0 lo dejo a 0
                $sql = "UPDATE productos SET cantidad=0 WHERE idProducto='$id_producto'";
                $conexion->query($sql);
            }
            // Actualizamos la cantidad del producto en la cesta, dependiendo del numero de unidades
            if ($cantidadOriginal > $unidades) {
                // Si hay suficiente stock, actualizamos la cantidad en la cesta
                $sql = "UPDATE productosCestas SET cantidad='$nuevaCantidad' WHERE idProducto='$id_producto' AND idCesta='$idCesta'";
                $conexion->query($sql);
            } else {
                // Si no hay suficiente stock, añadimos la cantidad disponible a la cesta
                $sql = "UPDATE productosCestas SET cantidad=cantidad+$cantidadOriginal WHERE idProducto='$id_producto' AND idCesta='$idCesta'";
                $conexion->query($sql);
            }            
        } else {
            // El producto no está en la cesta, lo añadimos
            $sql = "INSERT INTO productosCestas (idProducto, idCesta, cantidad) VALUES ('$id_producto','$idCesta','$unidades')";
            $conexion->query($sql);
    
            // Actualizamos la cantidad en la base de datos de productos
            $consultaProducto = "SELECT cantidad FROM productos WHERE idProducto='$id_producto'";
            $resultadoProducto = $conexion->query($consultaProducto);
            $filaProd = $resultadoProducto->fetch_assoc();
            //Stock actual
            $cantidadOriginal = $filaProd["cantidad"];
            //Stock tras modificar
            $nuevaCantidadProductos = $cantidadOriginal - $unidades;
            if ($nuevaCantidadProductos > 0){
                //Si es positivo actualizo al nuevo valor
                $sql = "UPDATE productos SET cantidad='$nuevaCantidadProductos' WHERE idProducto='$id_producto'";
                $conexion->query($sql);
            }else{
                //Si es negativo o 0 lo dejo a 0
                $sql = "UPDATE productos SET cantidad=0 WHERE idProducto='$id_producto'";
                $conexion->query($sql);
            }
            // Actualizamos la cantidad del producto en la cesta, dependiendo del numero de unidades
            if ($cantidadOriginal > $unidades) {
                // Si hay suficiente stock, actualizamos la cantidad en la cesta
                $sql = "UPDATE productosCestas SET cantidad='$unidades' WHERE idProducto='$id_producto' AND idCesta='$idCesta'";
                $conexion->query($sql);
            } else {
                // Si no hay suficiente stock, añadimos lo disponible
                $sql = "UPDATE productosCestas SET cantidad='$cantidadOriginal' WHERE idProducto='$id_producto' AND idCesta='$idCesta'";
                $conexion->query($sql);
            }         
        }
        //Para actualizar la página
        echo '<script>window.location.href = "listadoProductos.php";</script>';
    }
    
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>