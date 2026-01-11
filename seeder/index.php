<?php
/**
 * SEEDER NO ALEATORIO – FP 2018/2019
 * php seeder/index.php
 */

if (php_sapi_name() !== 'cli') {
    exit("Solo CLI\n");
}

require_once __DIR__ . '/../config/database.php';

$uploadsDir = __DIR__ . '/../uploads/';
$videosDir  = __DIR__ . '/videos/';
$csvFile    = __DIR__ . '/Taules_cataleg_FP_18-19-LOE.csv';

echo "🚀 Seeder determinista iniciado\n";

/* -------------------------------------------------
   1. FAMILIAS PROFESIONALES FIJAS
-------------------------------------------------- */

$familiasPermitidas = [
    'Informàtica i comunicacions',
    'Administració i gestió',
    'Electricitat i electrònica',
    'Sanitat',
    'Comerç i màrqueting',
    'Fabricació mecànica'
];

$famCicles = [];

/* Leer CSV */
if (($h = fopen($csvFile, 'r')) !== false) {
    fgetcsv($h, 0, ';'); // header
    while (($row = fgetcsv($h, 0, ';')) !== false) {
        $familia = trim($row[1]);
        $cicle   = trim($row[2]);

        if (in_array($familia, $familiasPermitidas)) {
            $famCicles[$familia][] = $cicle;
        }
    }
    fclose($h);
}

/* -------------------------------------------------
   2. TAGS (familias → ciclos)
-------------------------------------------------- */

echo "🏷️ Creando tags FP...\n";

$tagCicles = [];

foreach ($famCicles as $familia => $cicles) {

    $pdo->prepare(
        "INSERT INTO tags (nom, parent_id, tipus)
         VALUES (?, NULL, 'familia')"
    )->execute([$familia]);

    $familiaId = $pdo->lastInsertId();

    foreach (array_unique($cicles) as $cicle) {
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
    'Institut Tecnològic Barcelona',
    'Institut FP Girona',
    'Institut La Ribera',
    'Institut Montsià',
    'Institut Vallès',
    'Institut Joan XXIII',
    'Institut Delta',
    'Institut Baix Camp',
    'Institut Manresa',
    'Institut Lleida FP',
    'Institut Tarragonès',
    'Institut Ebre',
    'Institut Maresme',
    'Institut Garrotxa',
    'Institut Osona',
    'Institut Penedès',
    'Institut Priorat',
    'Institut Segrià',
    'Institut Berguedà',
    'Institut Ripollès'
];

$centreIds = [];

foreach ($centres as $i => $nom) {
    $pdo->prepare(
        "INSERT INTO centres (nom, email, descripcio, logo)
         VALUES (?, ?, ?, ?)"
    )->execute([
        $nom,
        strtolower(str_replace(' ', '', $nom)) . '@edu.cat',
        "Centre de Formació Professional especialitzat en projectes reals.",
        "centre" . ($i+1) . ".png"
    ]);

    $centreIds[] = $pdo->lastInsertId();
}

/* -------------------------------------------------
   4. EMPRESAS (20 FIJAS)
-------------------------------------------------- */

echo "🏢 Creando empresas...\n";

for ($i = 1; $i <= 20; $i++) {
    $pdo->prepare(
        "INSERT INTO empreses (nom, email, descripcio, logo)
         VALUES (?, ?, ?, ?)"
    )->execute([
        "Empresa FP {$i}",
        "empresa{$i}@empresa.cat",
        "Empresa col·laboradora amb centres de FP.",
        "empresa{$i}.png"
    ]);
}

/* -------------------------------------------------
   5. VÍDEOS (6 FIJOS, 6 CENTROS)
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

    $dest = "fp_video_" . ($i+1) . ".mp4";
    copy($videosDir . '/' . $v['file'], $uploadsDir . '/' . $dest);

    $pdo->prepare(
        "INSERT INTO videos (centre_id, titol, descripcio, fitxer, durada)
         VALUES (?, ?, ?, ?, 5)"
    )->execute([
        $centreIds[$i],
        "Projecte FP " . ($i+1),
        "Projecte real del centre " . $centres[$i],
        $dest
    ]);

    $videoId = $pdo->lastInsertId();

    /* Asignar TODOS los ciclos de su familia */
    foreach ($tagCicles[$v['familia']] as $tagId) {
        $pdo->prepare(
            "INSERT INTO video_tags (video_id, tag_id)
             VALUES (?, ?)"
        )->execute([$videoId, $tagId]);
    }
}

echo "✅ Seeder completado (determinista)\n";
