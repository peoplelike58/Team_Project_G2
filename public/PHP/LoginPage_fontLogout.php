<?php

session_start();

// 清掉 session
$_SESSION['member'] = [];
session_unset();
session_destroy();

echo json_encode(['success' => true, 'msg' => '已登出']);

?>