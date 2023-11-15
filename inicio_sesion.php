<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require 'conexionTienda.php'?>
    <?php require 'util.php'?>
</head>
<body>
<?php
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $usuario=depurar($_POST["usuario"]);
        $contrasena=depurar($_POST["password"]);

        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado=$conexion -> query ($sql);
        if ($resultado -> num_rows ===0){
            echo "El usuario no existe";
        }else{ 
            while ($fila=$resultado -> fetch_assoc()){
                $cypher_pass=$fila["contrasena"];
            }

            $acceso_valido=password_verify($contrasena,$cypher_pass);
            if($acceso_valido){
                session_start();
                $_SESSION["usuario"]=$usuario;
                header('location: listadoProductos.php');
            }else{
                echo "Has olvidado la contraseña";
            }
        }
    }
    ?>
    <div class="container">
        <h1 style="text-align:center; margin:20px;">Inicio de sesion</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="password">
            </div>
            <input class="btn btn-primary" type="submit" value="Iniciar sesion">
        </form>
        <button class="btn btn-dark" style= "float:left; margin:10px">
                    <a href="registroUsuario.php" style="text-decoration:none; color:white">Regístrate</a>
        </button>
        <a class="btn btn-dark"  href="listadoProductos.php" style= "float:right; margin:10px;text-decoration:none; color:white">
                    Acceder como invitado
        </a>
    </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>