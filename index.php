<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SALVAJES OUTDOORS</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('1.png') no-repeat center center fixed; /* Imagen de fondo */
            background-size: cover;
            height: 100vh;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .container {
            text-align: center;
        }

        h1 {
            font-size: 4rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
        }

        h2 {
            font-size: 1.5rem;
            font-weight: lighter;
            margin: 10px 0;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            position: relative;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Animación de desvanecimiento */
        .fade-out {
            animation: fadeOut 1s forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
                transform: scale(0.95);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>RUNNING Y SENDERISMO</h2>
        <h1>SALVAJES OUTDOORS</h1>
        <h2>PLANES DE ENTRENAMIENTO PERSONALIZADOS</h2>
        <button class="btn" onclick="redirectWithAnimation()">Únete Ahora</button>
    </div>

    <script>
        function redirectWithAnimation() {
            // Añadir clase para la animación
            document.body.classList.add('fade-out');
            
            // Redirigir después de que la animación termine
            setTimeout(() => {
                window.location.href = "login.php";
            }, 1000); // 1 segundo para la animación
        }
    </script>
</body>
</html>
