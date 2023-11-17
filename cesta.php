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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Para vaciar la cesta
        if (isset($_POST["vaciarCesta"])) {
            // Productos de mi cesta
            $productosDeCesta = "SELECT idProducto, cantidad FROM productosCestas WHERE idCesta = '$idCesta'";
            $losProductos = $conexion->query($productosDeCesta);
        
            while ($fila = $losProductos->fetch_assoc()) {
                $idProductoCesta = $fila['idProducto'];
                $cantidadCesta = $fila['cantidad'];
        
                // Actualizar la cantidad del producto
                $cantidadOriginal = "SELECT cantidad FROM productos WHERE idProducto = '$idProductoCesta'";
                $resultadoCantidadOriginal = $conexion->query($cantidadOriginal);
        
                if ($resultadoCantidadOriginal->num_rows > 0) {
                    $filaCantidadOriginal = $resultadoCantidadOriginal->fetch_assoc();
                    $cantidadOriginalProducto = $filaCantidadOriginal["cantidad"];
                    $nCantidadProd = $cantidadOriginalProducto + $cantidadCesta;
        
                    // Actualizar la cantidad en la base de datos de productos
                    $sql = "UPDATE productos SET cantidad='$nCantidadProd' WHERE idProducto='$idProductoCesta'";
                    $conexion->query($sql);
                }
            }
        
            // Eliminar todos los productos de la cesta
            $sql = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion->query($sql);
        }

        // Para eliminar un producto en particular
        if (isset($_POST["unidades"], $_POST["productoEliminar"], $_POST["cantidadActual"])) {
            $idProductoEliminar = $_POST['productoEliminar'];
            $unidades = $_POST['unidades'];
            $cantidadActual = $_POST["cantidadActual"];
            $cantidadTotal = $cantidadActual - $unidades;

            if ($cantidadTotal > 0) {
                // Si la nueva cantidad es positiva, actualizamos la tabla
                $sql = "UPDATE productosCestas SET cantidad = $cantidadTotal WHERE idProducto='$idProductoEliminar'";
                $conexion->query($sql);
            } else {
                // Si la nueva cantidad es 0 o negativa, eliminamos el producto
                $sql = "DELETE FROM productosCestas WHERE idProducto='$idProductoEliminar'";
                $conexion->query($sql);
                $unidades=$cantidadActual;
            }

            // Actualizar la cantidad en la base de datos de productos
            $cantidadOriginal = "SELECT cantidad FROM productos WHERE idProducto = '$idProductoEliminar'";
            $resultadoCantidadOriginal = $conexion->query($cantidadOriginal);

            if ($resultadoCantidadOriginal->num_rows > 0) {
                $filaCantidadOriginal = $resultadoCantidadOriginal->fetch_assoc();
                $cantidadOriginalProducto = $filaCantidadOriginal["cantidad"];
                $nCantidadProd = $cantidadOriginalProducto + $unidades;

                // Actualizar la cantidad en la base de datos de productos
                $sql = "UPDATE productos SET cantidad='$nCantidadProd' WHERE idProducto='$idProductoEliminar'";
                $conexion->query($sql);
            }
        }
    }
    //Para confirmar el pedido
    if (isset($_POST['finalizarCompra'])){
        $pedido=$_POST['finalizarCompra'];      
    }
    //Consulta para obtener los datos de la cesta
    $consultaCesta = "SELECT productosCestas.idProducto, productos.nombreProducto, productos.imagen, productosCestas.cantidad, productos.precio
                      FROM productosCestas
                      INNER JOIN productos ON productosCestas.idProducto = productos.idProducto
                      WHERE productosCestas.idCesta = '$idCesta'";
    $resultadoCesta = $conexion->query($consultaCesta);
    ?>
    <div class="container">
        <h1 style="text-align:center; margin:20px;">Cesta de <?php echo $usuario?></h1>
        <?php
        if (isset($_SESSION["usuario"])){?>
            <a class="btn btn-dark" href="cerrarsesion.php" style= "float:right; margin:10px; text-decoration:none; color:white">
            Cerrar sesión
            </a>
            <a class="btn btn-dark" href="listadoProductos.php" style= "float:right; margin:10px; text-decoration:none; color:white">
            Seguir comprando
            </a><?php
        }?>
        <?php $precioTotal=0; ?>
        <table class="table table-primary">
            <thead class="table-dark">
                <tr>
                    <th>Producto</th>
                    <th>Imagen</th>
                    <th>Cantidad</th>
                    <th>Precio por unidad</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php
            while ($filaCesta = $resultadoCesta->fetch_assoc()) {?>
                <tr>
                    <td><?php echo $filaCesta['nombreProducto']?></td>
                    <td><img height="80" src="<?php echo $filaCesta['imagen']?>"></td>
                    <td><?php echo $filaCesta['cantidad']?></td>
                    <td><?php echo ($filaCesta['precio'])?></td>
                    <td>
                        <?php $precioTotal+=$filaCesta['precio']*$filaCesta['cantidad']?>
                        <form action="" method="post">
                            <input type="hidden" name="productoEliminar" value="<?php echo $filaCesta['idProducto']?>">
                            <input type="hidden" name="cantidadActual" value="<?php echo $filaCesta['cantidad']?>">
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
                            <input class="btn btn-warning" type="submit" value="Eliminar de la cesta">
                        </form>
                    </td>
                </tr><?php
            }?>
            </tbody><?php
            if ($precioTotal>0){?>
                <tfoot class="table-danger">
                    <tr>
                        <td style="text-align: center;" colspan="5"><b>Precio total: <?php echo $precioTotal; ?>€</b></td>
                    </tr>
                </tfoot><?php
            }?>
        </table><?php
        if ($precioTotal>0){?>
            <form method="post" style="float: right;">
                    <button class="btn btn-danger" name="vaciarCesta" type="submit">Vaciar Cesta</button>
            </form>
            <form method="post" action="">
                <button class="btn btn-success" name="finalizarCompra" type="submit">Finalizar Pedido</button>
            </form><?php
        }?>
    </div>
    <?php 
    //Para insertar el pedido
    if (isset($pedido)){
        $sql = "INSERT INTO pedidos (usuario, precioTotal) 
                              VALUES ('$usuario', $precioTotal)";
        $conexion->query($sql);   
        //Método para obtener el id del pedido que acabamos de generar 
        $idPedido = $conexion->insert_id;  
        //Para insertar los datos en la tabla lineasPedidos
        $productosDeCesta = "SELECT pc.idProducto, pc.cantidad, p.precio
                           FROM productosCestas pc
                           JOIN productos p ON pc.idProducto = p.idProducto
                           WHERE pc.idCesta = '$idCesta'";
        $losProductos = $conexion->query($productosDeCesta);
        //Todos los pedidos empiezan en la linea de pedido 1
        $lineaPedido=1;
        while ($fila = $losProductos->fetch_assoc()) {
            $idProducto = $fila['idProducto'];
            $cantidad = $fila['cantidad'];
            $precioUnitario = $fila['precio'];
            // Insertamos en la tabla lineasPedidos
            $sql = "INSERT INTO lineasPedidos (lineaPedido, idProducto, idPedido, precioUnitario, cantidad) 
                    VALUES ($lineaPedido,'$idProducto', '$idPedido', $precioUnitario, $cantidad)";

            $conexion->query($sql);
            $lineaPedido++;
        }
        //Para vaciar la cesta
        $sqlEliminarProductos = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
        $conexion->query($sqlEliminarProductos);
        //Para actualizar la página
        echo '<script>window.location.href = "cesta.php";</script>';
    }?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>