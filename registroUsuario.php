<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require 'util.php' ?>
    <?php require 'conexionTienda.php' ?>
</head>
<body>
    <!--Validaciones Formulario-->
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //Variables temporales
        $tmp_usuario=depurar($_POST["usuario"]);
        $tmp_password=depurar($_POST["password"]);
        $tmp_fecha=depurar($_POST["fecha"]);

        //Comprobación de que el usuario cumple los requisitos:
        //4-12 solo letras y _
        if (strlen($tmp_usuario)==0){
            $err_usuario="CAMPO OBLIGATORIO";
        }else{
            $regex="/^[a-zA-Z_]{4,12}$/";
            if(!preg_match($regex, $tmp_usuario)) {
                $err_usuario = "El nombre de usuario debe contener cualquier combinación de 4 a 12
                    caracteres siendo estos tanto minúsculas, mayúsculas y la _";
            }else{
                $usuario=$tmp_usuario;
            }
        }

        //Comprobación de que la contraseña cumple los requisitos, 
        //un maximo de 255 caracteres
        if (strlen($tmp_password)==0){
            $err_password="CAMPO OBLIGATORIO";
        }else{
            if (strlen($tmp_password)>255){
                $err_password="Has sobrepasado la cantidad de caracteres maxima(255)";
            }else{
                $regex="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,20}$/";
                if(!preg_match($regex, $tmp_password)){
                    $err_password = "La contraseña debe contener cualquier combinación de 8 a 20
                    caracteres incluyendo al menos 1 minúscula, 1 mayúscula, 1 número y un carácter especial";
                }else{
                    $password=$tmp_password;
                }
            }
        }

        //Comprobación de que la fecha cumple los requisitos, debe
        //estar entre los -120 y los -12 años de la fecha actual
        //fecha<120 y fecha >12
        if (strlen($tmp_fecha)==0){
            $err_fecha="CAMPO OBLIGATORIO";
        }else{
            $dt = new DateTime($tmp_fecha);
            $fecha_actual = new DateTime();
            $edad = $fecha_actual->diff($dt)->y;
            if ($edad<12||$edad>120){
                if ($edad<12) $err_fecha="Eres muy joven";
                if ($edad>120) $err_fecha="Eres muy mayor";
            }
            else{
                $fecha=$tmp_fecha;
            }
        }
    }
    ?>
    <!--Formulario-->
    <div class="container">
        <h1 style="text-align:center; margin:20px;">Crea una cuenta</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Nombre de usuario</label>
                <input class="form-control" type="text" name="usuario">
                <?php 
                if(isset($err_usuario)) { ?>
                    <div>
                        <?php echo $err_usuario ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="password">
                <?php 
                if(isset($err_password)) { ?>
                    <div>
                        <?php echo $err_password ?>
                    </div>
                <?php
                } ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de nacimiento</label>
                <input class="form-control" type="date" name="fecha">
                <?php 
                if(isset($err_fecha)) { ?>
                    <div>
                        <?php echo $err_fecha ?>
                    </div>
                <?php
                } ?>
            </div>
            <br><br>
            <input type="submit" class="btn btn-primary" value="Enviar">
        </form>

        <a class="btn btn-dark"  href="inicio_sesion.php" style= "float:right; margin:10px;text-decoration:none; color:white">
        Ya tengo cuenta
        </a>
        <!--Comprobación-->
        
        <?php
        if(isset($usuario) && isset($password) && isset($fecha)) {
            echo "<h3>Usuario: $usuario</h3>";
            echo "<h3>Contraseña: $password</h3>";
            echo "<h3>Fecha: $fecha</h3>";
            $cypher_pass=password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento)
                VALUES ('$usuario','$cypher_pass','$fecha')";
            $conexion->query($sql);
            //Cesta autogenerada
            $sql = "INSERT INTO cestas (usuario, precioTotal)
                VALUES ('$usuario',0)";
            $conexion->query($sql);
            header('location: inicio_sesion.php');
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>