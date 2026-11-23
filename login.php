<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Inicia el buffer de salida para evitar problemas de redirección
ob_start();

include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $contraseña = trim($_POST['contraseña']);

    // Modificar la consulta para incluir el campo `id`
    $sql = "SELECT id, nombre_completo, nombre_usuario, contraseña, tipo_usuario, nivel FROM usuarios WHERE nombre_usuario = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->bind_result($id_usuario, $nombre_completo, $nombre_usuario, $hash_contraseña, $tipo_usuario, $nivel);

        if ($stmt->fetch()) {
            if ($contraseña === $hash_contraseña) { // Cambia por `password_verify` si las contraseñas están cifradas
                session_start(); // Reinicia la sesión
                $_SESSION['id_atleta'] = $id_usuario; // Guarda el ID del usuario
                $_SESSION['usuario'] = $nombre_usuario;
                $_SESSION['tipo'] = $tipo_usuario;
                $_SESSION['nombre_completo'] = $nombre_completo;
                $_SESSION['nivel'] = $nivel;

                // Redirige según el tipo de usuario
                if ($tipo_usuario === 'atleta') {
                    header("Location: dashboard_atleta.php");
                } else {
                    header("Location: dashboard_entrenador.php");
                }
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
        $stmt->close();
    } else {
        $error = "Error al preparar la consulta: " . $conn->error;
    }
}
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('1.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-container {
            background-color: rgba(30, 65, 86, 0.9);
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        .login-container h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            color: #AAB7B7;
        }

        .login-container label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-size: 1rem;
            font-weight: bold;
            color: #D4D8DD;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-radius: 5px;
            background-color: #2E4156;
            color: white;
            font-size: 1rem;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container p {
            font-size: 1rem;
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <form method="post" action="">
            <label for="usuario">Usuario:</label>
            <input type="text" name="usuario" id="usuario" required>
            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" id="contraseña" required>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
