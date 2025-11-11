<?php

require_once __DIR__ . '/../classes/DatabaseUsers.php';

class User
{
    // เพิ่ม mapping สำหรับ role ที่อนุญาต (ใช้ชื่อภาษาอังกฤษตาม login form)
    private static $allowedUserRoles = [
        'Teacher' => ['T', 'ADM', 'VP', 'OF', 'DIR'],
        'Officer' => ['ADM', 'OF'],
        'Director' => ['VP', 'DIR', 'ADM'],
        'Admin' => ['ADM'],
        'Student' => ['STU'],
        'Parent' => ['P']
    ];

    public static function authenticate($username, $password, $role)
    {
        $db = new \App\DatabaseUsers();

        // Login สำหรับนักเรียน
        if ($role === 'Student') {
            $student = $db->getStudentByUsername($username);
            if ($student) {
                // ถ้า Stu_password ว่าง ให้ return 'change_password'
                if (empty($student['Stu_password'])) {
                    return 'change_password';
                }
                // เปรียบเทียบรหัสผ่าน (รองรับทั้ง plain text และ hashed)
                if ($password === $student['Stu_password'] || password_verify($password, $student['Stu_password'])) {
                    // เพิ่ม role_std สำหรับความสอดคล้อง
                    $student['role_std'] = 'STU';
                    return $student;
                }
            }
            return false;
        }

        // Login สำหรับครู/เจ้าหน้าที่/ผู้บริหาร
        $user = $db->getTeacherByUsername($username);

        if ($user) {
            // ถ้า password ว่าง ให้ return 'change_password'
            if (empty($user['password'])) {
                return 'change_password';
            }
            // ตรวจสอบรหัสผ่านและ role
            if (
                password_verify($password, $user['password']) &&
                self::roleMatch($user['role_std'], $role)
            ) {
                return $user;
            }
        }
        return false;
    }

    // เพิ่มเมธอด static สำหรับดึงข้อมูลครู
    public static function getTeacherByUsername($username)
    {
        $db = new \App\DatabaseUsers();
        return $db->getTeacherByUsername($username);
    }

    // เพิ่มเมธอด static สำหรับดึงข้อมูลนักเรียน
    public static function getStudentByUsername($username)
    {
        $db = new \App\DatabaseUsers();
        return $db->getStudentByUsername($username);
    }

    // ตรวจสอบว่า role_std ของ user อยู่ในกลุ่ม role ที่เลือก
    private static function roleMatch($role_std, $role)
    {
        if (!isset(self::$allowedUserRoles[$role])) {
            return false;
        }
        return in_array($role_std, self::$allowedUserRoles[$role]);
    }

    // ตรวจสอบว่า user มีอยู่หรือไม่
    public static function userExists($username, $role)
    {
        $db = new \App\DatabaseUsers();
        
        if ($role === 'Student') {
            $student = $db->getStudentByUsername($username);
            return $student !== false;
        } else {
            $user = $db->getTeacherByUsername($username);
            return $user !== false;
        }
    }
}
