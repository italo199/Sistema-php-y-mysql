<?php
include("config.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit();
}

$mensaje = "";

$id = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);

    if (
        empty($nombre) ||
        empty($correo)
    ) {

        $mensaje = "Todos los campos son obligatorios.";

    } elseif (strlen($nombre) < 3) {

        $mensaje = "El nombre debe tener mínimo 3 caracteres.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Correo inválido.";

    } else {

        $sql = "SELECT cedula
                FROM usuarios
                WHERE correo = ?
                AND cedula != ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ss", $correo, $id);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "El correo ya está en uso.";

        } else {

            $sql = "UPDATE usuarios
                    SET nombre = ?, correo = ?
                    WHERE cedula = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sss",
                $nombre,
                $correo,
                $id
            );

            if ($stmt->execute()) {

                $_SESSION["nombre"] = $nombre;
                $_SESSION["correo"] = $correo;

                $mensaje = "Datos actualizados.";

            } else {

                $mensaje = "Error al actualizar.";
            }
        }
    }
}

$sql = "SELECT * FROM usuarios WHERE cedula = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $id);

$stmt->execute();

$usuario = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Perfil</title>

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
    background:#e5ffe5;
    color:green;
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

<h2>
Bienvenido
<?php echo htmlspecialchars($usuario["nombre"]); ?>
</h2>

<form method="POST">

<input type="text"
       name="nombre"
       value="<?php echo htmlspecialchars($usuario["nombre"]); ?>"
       required>

<input type="email"
       name="correo"
       value="<?php echo htmlspecialchars($usuario["correo"]); ?>"
       required>

<button type="submit">
    Actualizar Datos
</button>

</form>

<?php if($mensaje != ""){ ?>

<div class="mensaje">
    <?php echo htmlspecialchars($mensaje); ?>
</div>

<?php } ?>

<div class="link">

<a href="cambiar_password.php">
    Cambiar contraseña
</a>

<br><br>

<a href="logout.php">
    Cerrar sesión
</a>

</div>

</div>

</body>
</html>