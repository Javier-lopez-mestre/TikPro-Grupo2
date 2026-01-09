<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "cron.php";

if (isset($_SESSION['user_email'])) {
    header("Location: discover.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_id'] = $user['id'];
        header("Location: discover.php");
        exit;
    } else {
        $error = "Email o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body id="login-body">

<div id="login-container">
    <h2 id="login-title">Iniciar sesión</h2>

    <?php if ($error): ?>
        <p id="login-error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form id="login-form" method="post">
        <label id="label-email" for="email">Email</label>
        <input id="input-email" type="email" name="email" required>

        <label id="label-password" for="password">Contraseña</label>
        <input id="input-password" type="password" name="password" required>

        <button id="login-button" type="submit">Entrar</button>
    </form>
</div>

</body>
</html>
