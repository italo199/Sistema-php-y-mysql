<?php
include("config.php");

$mensaje = "";

function validarCedulaEcuatoriana($cedula) {

    if (strlen($cedula) != 10 || !ctype_digit($cedula)) {
        return false;
    }

    $provincia = intval(substr($cedula, 0, 2));

    if ($provincia < 1 || $provincia > 24) {
        return false;
    }

    $tercerDigito = intval($cedula[2]);

    if ($tercerDigito >= 6) {
        return false;
    }

    $suma = 0;

    for ($i = 0; $i < 9; $i++) {

        $digito = intval($cedula[$i]);

        if ($i % 2 == 0) {

            $digito *= 2;

            if ($digito > 9) {
                $digito -= 9;
            }
        }

        $suma += $digito;
    }

    $decenaSuperior = ceil($suma / 10) * 10;

    $digitoVerificador = $decenaSuperior - $suma;

    if ($digitoVerificador == 10) {
        $digitoVerificador = 0;
    }

    return $digitoVerificador == intval($cedula[9]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cedula = trim($_POST["cedula"]);
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $password = trim($_POST["password"]);

    if (
        empty($cedula) ||
        empty($nombre) ||
        empty($correo) ||
        empty($password)
    ) {

        $mensaje = "Todos los campos son obligatorios.";

    } elseif (!ctype_digit($cedula)) {

        $mensaje = "La cédula solo debe contener números.";

    } elseif (strlen($cedula) != 10) {

        $mensaje = "La cédula debe tener 10 dígitos.";

    } elseif (!validarCedulaEcuatoriana($cedula)) {

        $mensaje = "La cédula ecuatoriana no es válida.";

    } elseif (strlen($nombre) < 3) {

        $mensaje = "El nombre debe tener mínimo 3 caracteres.";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "Correo inválido.";

    } elseif (strlen($password) < 6) {

        $mensaje = "La contraseña debe tener mínimo 6 caracteres.";

    } else {

        $sql = "SELECT * FROM usuarios
                WHERE correo = ? OR cedula = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ss", $correo, $cedula);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {

            $mensaje = "La cédula o correo ya están registrados.";

        } else {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO usuarios
                    (cedula, nombre, correo, password)
                    VALUES (?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ssss",
                $cedula,
                $nombre,
                $correo,
                $passwordHash
            );

            if ($stmt->execute()) {

                $mensaje = "Usuario registrado correctamente.";

            } else {

                $mensaje = "Error al registrar usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Registro</title>

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
    color:#333;
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
    font-size:16px;
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
    border-radius:5px;
    background:#ffe5e5;
    color:#cc0000;
}

.link{
    margin-top:15px;
    text-align:center;
}

.link a{
    color:#007bff;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="contenedor">

<h2>Registro</h2>

<form method="POST">

<input type="text"
       name="cedula"
       placeholder="Cédula"
       maxlength="10"
       required>

<input type="text"
       name="nombre"
       placeholder="Nombre"
       required>

<input type="email"
       name="correo"
       placeholder="Correo"
       required>

<input type="password"
       name="password"
       placeholder="Contraseña"
       required>

<button type="submit">
    Registrarse
</button>

</form>

<?php if($mensaje != ""){ ?>

<div class="mensaje">
    <?php echo htmlspecialchars($mensaje); ?>
</div>

<?php } ?>

<div class="link">
    <a href="index.php">
        Ir al Login
    </a>
</div>

</div>

</body>
</html>