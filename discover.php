<?php
session_start();

include("config/database.php");

// === REDIRECCIONAR SI NO HI HA SESSIÓ ===
if (empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// ====== CARGAR PROJECTES DES DE LA BD ======
$stmt = $pdo->query("
    SELECT 
        id_project,
        description,
        video
    FROM projects;
");

$projects = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $projects[] = [
        "ID_Project" => $row["id_project"],
        "Description" => $row["description"],
        "Video" => $row["video"],
    ];
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
    <!-- Cards es crearan des de JS -->
</main>

<nav id="bottom-nav">
    <button id="nav-profile">👤</button>
    <button id="nav-chat">💬</button>
    <a href="logout.php" id="nav-logout" class="logout-button">🚪</a>
</nav>

<!-- Passar projectes a JS -->
<script>
    window.PROJECTS = <?= json_encode($projects, JSON_UNESCAPED_UNICODE) ?>;
    console.log(window.PROJECTS);
    console.log(document.getElementById('discover-container'));
</script>
<script src="discover.js"></script>
</body>
</html>
