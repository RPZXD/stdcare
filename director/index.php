<?php
session_start();
if (!isset($_SESSION['Director_login'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Director Dashboard</title>
    <!-- เพิ่ม CSS/JS ตามต้องการ -->
</head>
<body>
    <h1>ยินดีต้อนรับ ผู้อำนวยการ</h1>
    <p>นี่คือหน้าแรกของผู้อำนวยการ</p>
    <a href="../logout.php">ออกจากระบบ</a>
</body>
</html>
