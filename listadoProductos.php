<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de productos</title>
    <?php require "conexionTienda.php" ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
<body>
    <?php
        session_start();
        $usuario=$_SESSION["usuario"];
    ?>
    <div class="container">
        <h1>Listado Productos</h1>
        <p>Bienvenido <?php echo $usuario?> </p>
        <?php
        $sql = "SELECT * FROM productos ";
        $resultado = $conexion -> query($sql);
        ?>
        <table class="table table-primary">
            <thead class="table-dark">
                <tr>
                    <th>id</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while($fila=$resultado -> fetch_assoc()){
                    echo "<tr>";
                        echo "<td>".$fila["idProducto"]."</td>";
                        echo "<td>".$fila["nombreProducto"]."</td>";
                        echo "<td>".$fila["precio"]."</td>";
                        echo "<td>".$fila["descripcion"]."</td>";
                        echo "<td>".$fila["cantidad"]."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>