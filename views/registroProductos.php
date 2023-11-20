<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require '../util/util.php' ?>
    <?php require '../util/conexionTienda.php' ?>
</head>
<body>
    <?php
        session_start();
        if (isset($_SESSION['usuario'])) {
            $usuario = $_SESSION['usuario'];
            $rol = $_SESSION["rol"];
            if ($rol == "cliente") {
                header('location: listadoProductos.php');
            } 
        }else {
            header('location: listadoProductos.php');
        }
    ?>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //Variables temporales
        $tmp_producto=depurar($_POST["producto"]);
        $tmp_precio=depurar($_POST["precio"]);
        $tmp_descripcion=depurar($_POST["descripcion"]);
        $tmp_cantidad=depurar($_POST["cantidad"]);


        //Comprobación de que el nombre del producto cumple los requisitos
        if (strlen($tmp_producto)==0){
            $err_producto="CAMPO OBLIGATORIO";
        }else{
            $regex="/^[a-zA-Z1-9 ]{1,40}$/";
            if(!preg_match($regex, $tmp_producto)) {
                $err_producto = "Solo validos letras, numeros y espacios,
                máximo 40 caracteres";
            }else{
                $producto=$tmp_producto;
            }
        }

        //Comprobación de que el precio cumpla los requisitos
        if (strlen($tmp_precio)==0){
            $err_precio="CAMPO OBLIGATORIO";
        }else{
            if(filter_var($tmp_precio, FILTER_VALIDATE_FLOAT) === FALSE) {
                $err_precio = "Introduce números";
            } else {
                if($tmp_precio<0||$tmp_precio>99999.99){
                    if($tmp_precio<0) $err_precio = "Introduce números positivos";
                    if($tmp_precio>99999.99) $err_precio = "Introduce números menores a 99999,99";
                }else{
                    $precio=$tmp_precio;
                }
            }
        }

        //Comprobación de que la descripción cumple los requisitos
        if (strlen($tmp_descripcion)==0){
            $err_descripcion="CAMPO OBLIGATORIO";
        }else{
            if (strlen($tmp_descripcion)>255){
                $err_descripcion="Has sobrepasado la cantidad de caracteres maxima(255)";
            }else{
                $descripcion=$tmp_descripcion;
            }
        }

        //Comprobación de que la cantidad cumpla los requisitos
        if (strlen($tmp_cantidad)==0){
            $err_cantidad="CAMPO OBLIGATORIO";
        }else{
            if(filter_var($tmp_cantidad, FILTER_VALIDATE_INT) === FALSE) {
                $err_precio = "Introduce números";
            } else {
                if($tmp_cantidad<0||$tmp_cantidad>99999){
                    if($tmp_cantidad<0) $err_cantidad = "Introduce números positivos";
                    if($tmp_cantidad>99999) $err_cantidad = "Introduce números menores a 99999,99";
                }else{
                    $cantidad=$tmp_cantidad;
                }
            }
        }

        $nombre_fichero = $_FILES["imagen"]["name"];
        if (strlen($nombre_fichero)==0){
            $err_imagen="CAMPO OBLIGATORIO";
        }else{
            $tipo=$_FILES["imagen"]["type"];
            if ($tipo!="image/jpeg"&&$tipo!="image/jpg"&&$tipo!="image/png"){
                $err_imagen="Formato incorrecto";
            }else{
                $tam=$_FILES["imagen"]["size"];//Devuelve el tamaño en bytes
                if ($tam>5242880){
                    $err_imagen="Tamaño maximo 5MB";
                }
                else{
                    $ruta_temporal=$_FILES["imagen"]["tmp_name"];
                    $ruta = "imagenes/".$nombre_fichero;
                    move_uploaded_file($ruta_temporal,$ruta);
                }
            }
        }
    }
    ?>
    <!--Formulario-->
    <h1 style="text-align:center; margin:20px;">Registro de Productos</h1>
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nombre del producto</label>
                <input class="form-control" type="text" name="producto">
                <?php 
                if(isset($err_producto)) { ?>
                    <div>
                        <?php echo $err_producto ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input class="form-control" type="text" name="precio">
                <?php 
                if(isset($err_precio)) { ?>
                    <div>
                        <?php echo $err_precio ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <input class="form-control" type="text" name="descripcion">
                <?php 
                if(isset($err_descripcion)) { ?>
                    <div>
                        <?php echo $err_descripcion ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input class="form-control" type="text" name="cantidad">
                <?php 
                if(isset($err_cantidad)) { ?>
                    <div>
                        <?php echo $err_cantidad ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input class="form-control" type="file" name="imagen">
                <?php 
                if(isset($err_imagen)) { ?>
                    <div>
                        <?php echo $err_imagen ?>
                    </div>
                <?php
                } ?>
            </div>
            <br><br>
            <input class="btn btn-primary" type="submit" value="Enviar">
        </form>
        <a class="btn btn-dark" href="../util/cerrarsesion.php" style= "float:right; margin:10px; text-decoration:none; color:white">
            Cerrar sesión
        </a>
        <a class="btn btn-dark" href="listadoProductos.php" style= "margin:10px; text-decoration:none; color:white">
        Página Principal
        </a>
        <!--Comprobación-->
        <?php
        if(isset($producto) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($ruta)) {
            $sql = "INSERT INTO productos (nombreProducto, precio, descripcion, cantidad, imagen)
                VALUES ('$producto','$precio','$descripcion','$cantidad', '$ruta')";
            $conexion->query($sql);
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>