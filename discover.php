<<<<<<< Updated upstream
=======
<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descobrir</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body id="discover-body">

<main id="discover-container">
    <!-- Cards creadas dinámicamente -->
</main>

<nav id="bottom-nav">
    <button id="nav-profile">👤</button>
    <button id="nav-chat">💬</button>
    <button id="nav-details">ℹ️</button>
</nav>

<script src="discover.js"></script>
</body>
</html>
>>>>>>> Stashed changes
