<?php
include("config.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = trim($_POST["correo"]);
    $password = trim($_POST["password"]);

    if (
        empty($correo) ||
        empty($password)
    ) {

        $mensaje = "Complete todos los campos.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Correo inválido.";

    } else {

        $sql = "SELECT * FROM usuarios WHERE correo = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $correo);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {

            $usuario = $resultado->fetch_assoc();

            if (
                password_verify(
                    $password,
                    $usuario["password"]
                )
            ) {

                session_regenerate_id(true);

                $_SESSION["usuario_id"] = $usuario["cedula"];
                $_SESSION["nombre"] = $usuario["nombre"];
                $_SESSION["correo"] = $usuario["correo"];

                header("Location: perfil.php");
                exit();

            } else {

                $mensaje = "Contraseña incorrecta.";
            }

        } else {

            $mensaje = "Usuario no encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Login</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#f4f6f9;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.contenedor{
    background:white;
    width:380px;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 15px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    width:100%;
    padding:12px;
    margin-top:20px;
    border:none;
    background:#007bff;
    color:white;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#0056b3;
}

.mensaje{
    margin-top:15px;
    text-align:center;
    padding:10px;
    background:#ffe5e5;
    color:#cc0000;
    border-radius:5px;
}

.link{
    margin-top:15px;
    text-align:center;
}

.link a{
    text-decoration:none;
    color:#007bff;
}

</style>

</head>

<body>

<div class="contenedor">

<h2>Iniciar Sesión</h2>

<form method="POST">

<input type="email"
       name="correo"
       placeholder="Correo"
       required>

<input type="password"
       name="password"
       placeholder="Contraseña"
       required>

<button type="submit">
    Ingresar
</button>

</form>

<?php if($mensaje != ""){ ?>

<div class="mensaje">
    <?php echo htmlspecialchars($mensaje); ?>
</div>

<?php } ?>

<div class="link">

<a href="registro.php">
    Crear cuenta
</a>

</div>

</div>

</body>
</html>