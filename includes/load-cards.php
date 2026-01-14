<?php
    session_start();
    include("database.php");

    // indicar que la respuesta es json
    header('Content-Type: application/json');

    // comprobar sesión
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            "error" => "Sessió no iniciada"
        ]);
        exit;
    }

    $excludeId = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : 0;

    try {
        // consulta
        $stmt = $pdo->prepare("
            SELECT p.id_project, p.title, p.description, p.video
            FROM projects p
            WHERE p.id_project != :excludeId
            ORDER BY RAND()
            LIMIT 1;
        ");
        $stmt->execute(['excludeId' => $excludeId]);

        while ($row = $stmt->fetch()) {
            $projects = [
                "id_project" => $row["id_project"],
                "description" => $row["description"],
                "video" => $row["video"]
            ];
        }

        // sin resultados
        if (empty($projects)) {
            http_response_code(204);
            exit;
        }

        // todo correcto
        http_response_code(200);
        echo json_encode($projects);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "error" => "No s'ha pogut conectar amb el servidor"
        ]);
    }
?>