<?php

include 'conn.php'

session_start();



$payload = json_decode(file_get_contents('php://input'), true);
$me = intval($payload['MESSAGE_ID'] ?? 0);

$sql = "DELETE FROM MESSAGE WHERE MESSAGE_ID = :me"

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':me',$me , PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success'=>true, 'data'=>$rows], JSON_UNESCAPED_UNICODE);



?>