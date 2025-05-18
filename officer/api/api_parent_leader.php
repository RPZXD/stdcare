<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/BoardParent.php");

$pee = isset($_GET['pee']) ? $_GET['pee'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';

if (!$pee) {
    echo json_encode(['data' => []]);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$boardParent = new BoardParent($db);

try {
    $sql = "SELECT DISTINCT parn_lev, parn_room FROM tb_parnet WHERE parn_pee = :pee";
    if ($level !== '') {
        $sql .= " AND parn_lev = :level";
    }
    $sql .= " ORDER BY parn_lev, parn_room";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
    if ($level !== '') {
        $stmt->bindParam(':level', $level, PDO::PARAM_STR);
    }
    $stmt->execute();
    $classRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $leaders = [];
    foreach ($classRooms as $cr) {
        $class = $cr['parn_lev'];
        $room = $cr['parn_room'];
        $rows = $boardParent->getBoardParentByClassAndRoom($class, $room, $pee);
        foreach ($rows as $row) {
            if ($row['parn_pos'] == 1) {
                $leaders[] = $row;
            }
        }
    }
    echo json_encode(['data' => $leaders]);
} catch (Exception $e) {
    echo json_encode(['data' => []]);
}
?>
