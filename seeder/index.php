<?php
/**
 * SEEDER DETERMINISTA – FP
 * Ejecutar: php seeder/index.php
 */

if (php_sapi_name() !== 'cli') {
    exit("Solo se puede ejecutar desde CLI\n");
}

require_once __DIR__ . '/../config/database.php';

$uploadsDir = __DIR__ . '/../uploads/';
$videosDir  = __DIR__ . '/videos/';

echo "🚀 Iniciando seeder...\n";

$pdo->beginTransaction();

try {

/* -------------------------------------------------
   0. LIMPIAR BASE DE DATOS
-------------------------------------------------- */
echo "🧹 Limpiando base de datos...\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE video_tags");
$pdo->exec("TRUNCATE videos");
$pdo->exec("TRUNCATE tags");
$pdo->exec("TRUNCATE centres");
$pdo->exec("TRUNCATE empreses");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

/* -------------------------------------------------
   1. FAMILIAS Y CICLOS (FIJOS)
-------------------------------------------------- */
$famCicles = [
    'Informàtica i comunicacions' => [
        'SMX', 'ASIX', 'DAM', 'DAW'
    ],
    'Administració i gestió' => [
        'Gestió Administrativa',
        'Administració i Finances'
    ],
    'Electricitat i electrònica' => [
        'Instal·lacions Elèctriques',
        'Automatització i Robòtica'
    ],
    'Sanitat' => [
        'Cures Auxiliars d’Infermeria',
        'Laboratori Clínic'
    ],
    'Comerç i màrqueting' => [
        'Activitats Comercials',
        'Màrqueting i Publicitat'
    ],
    'Fabricació mecànica' => [
        'Mecanitzat',
        'Programació de la Producció'
    ]
];

/* -------------------------------------------------
   2. TAGS (familias y ciclos)
-------------------------------------------------- */
echo "🏷️ Creando tags...\n";

$tagCicles = [];

foreach ($famCicles as $familia => $cicles) {

    $pdo->prepare(
        "INSERT INTO tags (nom, parent_id, tipus)
         VALUES (?, NULL, 'familia')"
    )->execute([$familia]);

    $familiaId = $pdo->lastInsertId();

    foreach ($cicles as $cicle) {
        $pdo->prepare(
            "INSERT INTO tags (nom, parent_id, tipus)
             VALUES (?, ?, 'cicle')"
        )->execute([$cicle, $familiaId]);

        $tagCicles[$familia][] = $pdo->lastInsertId();
    }
}

/* -------------------------------------------------
   3. CENTROS (20 FIJOS)
-------------------------------------------------- */
echo "🏫 Creando centros...\n";

$centres = [
    'Institut Tecnològic de Barcelona',
    'Institut La Ribera',
    'Institut Montsià',
    'Institut Vallès',
    'Institut Joan XXIII',
    'Institut Delta',
    'Institut Baix Camp',
    'Institut Manresa',
    'Institut Lleida FP',
    'Institut Tarragonès',
    'Institut de l’Ebre',
    'Institut Maresme',
    'Institut Garrotxa',
    'Institut Osona',
    'Institut Penedès',
    'Institut Priorat',
    'Institut Segrià',
    'Institut Berguedà',
    'Institut Ripollès',
    'Institut Escola del Treball'
];

$centreIds = [];

foreach ($centres as $i => $nom) {

    $email = strtolower(preg_replace('/[^a-zA-Z]/', '', $nom)) . '@edu.cat';

    $pdo->prepare(
        "INSERT INTO centres (nom, email, descripcio, logo)
         VALUES (?, ?, ?, ?)"
    )->execute([
        $nom,
        $email,
        "Centre de Formació Professional amb projectes reals.",
        "centre" . ($i + 1) . ".png"
    ]);

    $centreIds[] = $pdo->lastInsertId();
}

/* -------------------------------------------------
   4. EMPRESAS (20 FIJAS)
-------------------------------------------------- */
echo "🏢 Creando empresas...\n";

$empreses = [
    'Google', 'Microsoft', 'Amazon', 'Apple', 'IBM',
    'HP', 'Intel', 'Accenture', 'Capgemini', 'Mercedes-Benz',
    'Indra', 'Siemens', 'PayPal', 'Deloitte', 'Coca-Cola',
    'YouTube', 'Telefónica', 'CaixaBank', 'Banc Sabadell', 'Nestlé'
];

foreach ($empreses as $i => $nom) {
    $pdo->prepare(
        "INSERT INTO empreses (nom, email, descripcio, logo)
         VALUES (?, ?, ?, ?)"
    )->execute([
        $nom,
        strtolower($nom) . '@empresa.com',
        "Empresa col·laboradora amb centres de FP.",
        "empresa" . ($i + 1) . ".png"
    ]);
}

/* -------------------------------------------------
   5. VÍDEOS (6 vídeos / 6 centros)
-------------------------------------------------- */
echo "🎬 Creando vídeos...\n";

$videos = [
    ['file' => 'video1.mp4', 'familia' => 'Informàtica i comunicacions'],
    ['file' => 'video2.mp4', 'familia' => 'Administració i gestió'],
    ['file' => 'video3.mp4', 'familia' => 'Electricitat i electrònica'],
    ['file' => 'video4.mp4', 'familia' => 'Sanitat'],
    ['file' => 'video5.mp4', 'familia' => 'Comerç i màrqueting'],
    ['file' => 'video6.mp4', 'familia' => 'Fabricació mecànica']
];

foreach ($videos as $i => $v) {

    $src  = $videosDir . $v['file'];
    $dest = "fp_video_" . ($i + 1) . ".mp4";

    if (!file_exists($src)) {
        throw new Exception("No existe el vídeo {$v['file']}");
    }

    copy($src, $uploadsDir . $dest);

    $pdo->prepare(
        "INSERT INTO videos (centre_id, titol, descripcio, fitxer, durada)
         VALUES (?, ?, ?, ?, 5)"
    )->execute([
        $centreIds[$i],
        "Projecte FP " . ($i + 1),
        "Projecte real del centre " . $centres[$i],
        $dest
    ]);

    $videoId = $pdo->lastInsertId();

    foreach ($tagCicles[$v['familia']] as $tagId) {
        $pdo->prepare(
            "INSERT INTO video_tags (video_id, tag_id)
             VALUES (?, ?)"
        )->execute([$videoId, $tagId]);
    }
}

$pdo->commit();
echo "✅ Seeder ejecutado correctamente\n";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
