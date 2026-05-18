<?php
include("config.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

$id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $actual = trim($_POST["actual"]);
    $nueva = trim($_POST["nueva"]);
    $confirmar = trim($_POST["confirmar"]);

    if (
        empty($actual) ||
        empty($nueva) ||
        empty($confirmar)
    ) {

        $mensaje = "Todos los campos son obligatorios.";

    } elseif (strlen($nueva) < 6) {

        $mensaje = "La nueva contraseña debe tener mínimo 6 caracteres.";

    } elseif ($nueva != $confirmar) {

        $mensaje = "Las contraseñas no coinciden.";

    } else {

        $sql = "SELECT password
                FROM usuarios
                WHERE cedula = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $id);

        $stmt->execute();

        $usuario = $stmt->get_result()->fetch_assoc();

        if (
            password_verify(
                $actual,
                $usuario["password"]
            )
        ) {

            $nuevoHash = password_hash(
                $nueva,
                PASSWORD_DEFAULT
            );

            $sql = "UPDATE usuarios
                    SET password = ?
                    WHERE cedula = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ss",
                $nuevoHash,
                $id
            );

            if ($stmt->execute()) {

                $mensaje = "Contraseña actualizada.";

            } else {

                $mensaje = "Error al actualizar.";
            }

        } else {

            $mensaje = "La contraseña actual es incorrecta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Cambiar Contraseña</title>

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
    width:400px;
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

<h2>Cambiar Contraseña</h2>

<form method="POST">

<input type="password"
       name="actual"
       placeholder="Contraseña actual"
       required>

<input type="password"
       name="nueva"
       placeholder="Nueva contraseña"
       required>

<input type="password"
       name="confirmar"
       placeholder="Confirmar contraseña"
       required>

<button type="submit">
    Actualizar Contraseña
</button>

</form>

<?php if($mensaje != ""){ ?>

<div class="mensaje">
    <?php echo htmlspecialchars($mensaje); ?>
</div>

<?php } ?>

<div class="link">

<a href="perfil.php">
    Volver al perfil
</a>

</div>

</div>

</body>
</html>