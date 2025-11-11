<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/TermPee.php';

class LoginController
{
    private $logger;

    public function __construct($logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * ตรวจสอบการ login และจัดการ session
     * @param string $username ชื่อผู้ใช้
     * @param string $password รหัสผ่าน
     * @param string $role บทบาท (Teacher, Admin, Officer, Director, Student)
     * @return array ['success' => bool, 'message' => string, 'redirect' => string]
     */
    public function login($username, $password, $role)
    {
        // ตรวจสอบว่า user มีอยู่หรือไม่
        if (!User::userExists($username, $role)) {
            $this->logLoginAttempt($username, $role, 401, 'User does not exist');
            
            if ($role === 'Student') {
                return [
                    'success' => false,
                    'message' => 'ไม่มีชื่อนักเรียนนี้',
                    'redirect' => 'login.php'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'ไม่มีชื่อผู้ใช้นี้',
                    'redirect' => 'login.php'
                ];
            }
        }

        // ตรวจสอบ authentication
        $user = User::authenticate($username, $password, $role);
        
        // กรณีต้องเปลี่ยนรหัสผ่าน
        if ($user === 'change_password') {
            $_SESSION['user'] = $username;
            $this->logLoginAttempt($username, $role, 302, 'Redirect to change password');
            return [
                'success' => true,
                'message' => 'กรุณาเปลี่ยนรหัสผ่าน',
                'redirect' => 'change_password.php'
            ];
        }

        // กรณี login สำเร็จ
        if ($user) {
            // ตั้งค่า session สำหรับนักเรียน
            if ($role === 'Student') {
                $stuStatus = $user['Stu_status'] ?? 1;
                
                // ตรวจสอบสถานะนักเรียน
                if ($stuStatus != 1) {
                    $this->logLoginAttempt($username, $role, 403, 'Student not active');
                    return [
                        'success' => false,
                        'message' => 'บัญชีนักเรียนไม่ได้เปิดใช้งาน',
                        'redirect' => 'login.php'
                    ];
                }

                // ตั้งค่า session สำหรับนักเรียน
                $_SESSION['user'] = $username;
                $_SESSION['Student_login'] = $username;
                
                $this->logLoginAttempt($username, $role, 200, 'Student login successful');
                
                return [
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับนักเรียน!',
                    'redirect' => 'student/index.php'
                ];
            } 
            // ตั้งค่า session สำหรับครู/เจ้าหน้าที่/ผู้บริหาร
            else {
                $_SESSION['user'] = $username;
                $_SESSION[$role . '_login'] = $username;
                
                // เพิ่มเก็บ term pee ลง session
                $termPee = TermPee::getCurrent();
                $_SESSION['term'] = $termPee->term;
                $_SESSION['pee'] = $termPee->pee;
                
                $this->logLoginAttempt($username, $role, 200, 'Login successful');
                
                // กำหนด redirect path ตาม role
                $redirectPaths = [
                    'Teacher' => 'teacher/index.php',
                    'Admin' => 'admin/index.php',
                    'Officer' => 'officer/index.php',
                    'Director' => 'director/index.php'
                ];
                
                $redirectPath = $redirectPaths[$role] ?? 'index.php';
                
                return [
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ!',
                    'redirect' => $redirectPath
                ];
            }
        } 
        // กรณีรหัสผ่านไม่ถูกต้อง
        else {
            $this->logLoginAttempt($username, $role, 401, 'Incorrect password or invalid role');
            return [
                'success' => false,
                'message' => 'พาสเวิร์ดไม่ถูกต้อง',
                'redirect' => 'login.php'
            ];
        }
    }

    /**
     * บันทึก log การพยายาม login
     */
    private function logLoginAttempt($username, $role, $statusCode, $message)
    {
        if ($this->logger) {
            $this->logger->log([
                "user_id" => $username,
                "role" => $role,
                "ip_address" => $_SERVER['REMOTE_ADDR'],
                "user_agent" => $_SERVER['HTTP_USER_AGENT'],
                "access_time" => date("c"),
                "url" => $_SERVER['REQUEST_URI'],
                "method" => $_SERVER['REQUEST_METHOD'],
                "status_code" => $statusCode,
                "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                "action_type" => "login_attempt",
                "session_id" => session_id(),
                "message" => $message
            ]);
        }
    }

    /**
     * ออกจากระบบ
     */
    public function logout()
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Log the logout action
        if ($this->logger && isset($_SESSION['user'])) {
            $this->logger->log([
                "user_id" => $_SESSION['user'],
                "role" => $this->getCurrentRole(),
                "ip_address" => $_SERVER['REMOTE_ADDR'],
                "user_agent" => $_SERVER['HTTP_USER_AGENT'],
                "access_time" => date("c"),
                "url" => $_SERVER['REQUEST_URI'],
                "method" => $_SERVER['REQUEST_METHOD'],
                "status_code" => 200,
                "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
                "action_type" => "logout",
                "session_id" => session_id(),
                "message" => "User logged out"
            ]);
        }
        
        // Clear all login sessions
        unset($_SESSION['Teacher_login']);
        unset($_SESSION['Admin_login']);
        unset($_SESSION['Officer_login']);
        unset($_SESSION['Director_login']);
        unset($_SESSION['Group_leader_login']);
        unset($_SESSION['Student_login']);
        unset($_SESSION['user']);
        
        session_write_close();
        
        return [
            'success' => true,
            'message' => 'คุณได้ออกจากระบบแล้ว',
            'redirect' => 'login.php'
        ];
    }

    /**
     * ดึง role ปัจจุบันจาก session
     */
    private function getCurrentRole()
    {
        $roles = ['Teacher', 'Admin', 'Officer', 'Director', 'Student'];
        foreach ($roles as $role) {
            if (isset($_SESSION[$role . '_login'])) {
                return $role;
            }
        }
        return null;
    }
}
