<?php
/**
 * StdCare System - Change Password Router
 * MVC Structure
 */

ob_start();
date_default_timezone_set('Asia/Bangkok');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not assigned to change password (must have 'user' session but no role login session)
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Load dependencies
require_once __DIR__ . '/classes/DatabaseUsers.php';
require_once __DIR__ . '/models/User.php';

use App\DatabaseUsers;

// Check if user already has a lead role (means they are already fully logged in)
$fullyLoggedIn = false;
$roles = ['Teacher_login', 'Admin_login', 'Officer_login', 'Director_login', 'Student_login'];
foreach ($roles as $role) {
    if (isset($_SESSION[$role])) {
        $fullyLoggedIn = true;
        break;
    }
}

// If already logged in, redirect away from here (unless they explicitly navigated to change it)
// But usually change_password.php as a root router is for forced changes.

$swalAlert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validation logic (English letters + Numbers, min 6, no Thai)
    if (strlen($newPassword) < 6 || !preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword) || preg_match('/[ก-๙]/u', $newPassword)) {
        $swalAlert = [
            'title' => 'รหัสผ่านไม่ปลอดภัย',
            'text' => 'ต้องมีความยาวอย่างน้อย 6 ตัวอักษร ประกอบด้วยตัวอักษรและตัวเลข และห้ามมีภาษาไทย',
            'icon' => 'error'
        ];
    } else if ($newPassword !== $confirmPassword) {
        $swalAlert = [
            'title' => 'รหัสผ่านไม่ตรงกัน',
            'text' => 'กรุณากรอกรหัสผ่านให้ตรงกันทั้งสองช่อง',
            'icon' => 'error'
        ];
    } else {
        // Success -> Update Database
        try {
            $database = new DatabaseUsers();
            $conn = $database->getPDO();
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            
            // Note: We need to determine if this is a student or teacher
            // In LoginController, $_SESSION['user'] contains the ID/Username
            // We can check Teacher first, then Student if needed
            
            $updated = false;
            
            // Try updating teacher first
            $stmt = $conn->prepare("UPDATE teacher SET password = :pass, Teach_password = '' WHERE Teach_id = :id");
            $stmt->execute(['pass' => $hashedPassword, 'id' => $_SESSION['user']]);
            
            if ($stmt->rowCount() > 0) {
                $updated = true;
            } else {
                // Try updating student
                $stmt = $conn->prepare("UPDATE student SET Stu_password = :pass WHERE Stu_id = :id");
                $stmt->execute(['pass' => $hashedPassword, 'id' => $_SESSION['user']]);
                if ($stmt->rowCount() > 0) {
                    $updated = true;
                }
            }

            if ($updated) {
                // Success - logout to let them log in with new password
                // Or we could auto-login, but logout is safer to ensure they remember it.
                $swalAlert = [
                    'title' => 'เปลี่ยนรหัสผ่านสำเร็จ',
                    'text' => 'กรุณาเข้าสู่ระบบด้วยรหัสผ่านใหม่',
                    'icon' => 'success',
                    'redirect' => 'login.php'
                ];
                // Optional: session_destroy() if you want to be extra safe
            } else {
                $swalAlert = [
                    'title' => 'เกิดข้อผิดพลาด',
                    'text' => 'ไม่พบข้อมูลผู้ใช้งาน หรือรหัสผ่านเดิมตรงกับของใหม่',
                    'icon' => 'warning'
                ];
            }
        } catch (Exception $e) {
            $swalAlert = [
                'title' => 'ข้อผิดพลาดระบบ',
                'text' => $e->getMessage(),
                'icon' => 'error'
            ];
        }
    }
}

// Prepare data for view
$title = 'เปลี่ยนรหัสผ่าน';

// Include view
include __DIR__ . '/views/auth/change_password.php';

ob_end_flush();