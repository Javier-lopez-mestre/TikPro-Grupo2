<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

include("database.php");

// ====== CARGAR PROYECTOS DESDE LA BD ======
$stmt = $pdo->query("
    SELECT 
        p.ID_Project,
        p.Description,
        p.Video,
        GROUP_CONCAT(c.name_category) AS tags
    FROM Projects p
    LEFT JOIN Projects_Categories pc ON p.ID_Project = pc.ID_Project
    LEFT JOIN Categories c ON pc.ID_Category = c.ID_Category
    WHERE p.State = 'Active'
    GROUP BY p.ID_Project
");

$projects = [];

while ($row = $stmt->fetch()) {
    $projects[] = [
        "ID_Project" => $row["ID_Project"],
        "Description" => $row["Description"],
        "Video" => $row["Video"],
        "tags" => $row["tags"] ? explode(",", $row["tags"]) : []
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
    <!-- Cards creadas dinámicamente -->
</main>

<nav id="bottom-nav">
    <button id="nav-profile">👤</button>
    <button id="nav-chat">💬</button>
    <button id="nav-details">ℹ️</button>
</nav>

<!-- PASAR LOS PROYECTOS A JS -->
<script>
    window.PROJECTS = <?= json_encode($projects, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="discover.js"></script>
</body>
</html>
