<?php
/**
 * Forwarder/Wrapper for upload_student_photo.php
 * Handles old/cached browser requests by mapping parameter keys
 * and routing the request to update_profile_pic_std.php.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Map old parameter keys to new keys expected by update_profile_pic_std.php
    if (isset($_POST['stu_id']) && !isset($_POST['Stu_id'])) {
        $_POST['Stu_id'] = $_POST['stu_id'];
    }
    
    if (isset($_FILES['photo']) && !isset($_FILES['profile_pic'])) {
        $_FILES['profile_pic'] = $_FILES['photo'];
    }
}

// Route to the main handler
require_once __DIR__ . '/update_profile_pic_std.php';
?>
