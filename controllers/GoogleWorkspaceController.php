<?php
namespace Controllers;

class GoogleWorkspaceController {
    // กำหนด URL ของ GAS Web App และ Token ที่นี่
    // แนะนำให้แอดมินแก้ไขหลังจาก Deploy GAS สำเร็จ
    private $gasUrl = "https://script.google.com/macros/s/AKfycbw2TpxxBNgBkfyJeDQlyTMwn8LAIn8LKN8ygIfyWXB4cNAAQ80gXK-BjvZzbQZweozR/exec"; 
    private $secretToken = "stdcare_phichai_secret_token_1234";

    public function __construct() {
        // สามารถโหลด Config จากไฟล์อื่นได้ถ้าต้องการ
    }

    /**
     * อัปเดตรหัสผ่าน Google Workspace
     *
     * @param string $email อีเมลนักเรียน (เช่น student.1234@phichai.ac.th)
     * @param string $newPassword รหัสผ่านใหม่
     * @return array ผลลัพธ์จากการเรียก API
     */
    public function updatePassword($email, $newPassword) {
        $payload = [
            "token" => $this->secretToken,
            "action" => "updatePassword",
            "email" => $email,
            "newPassword" => $newPassword
        ];

        return $this->sendRequest($payload);
    }

    /**
     * อัปเดตชื่อจริงและนามสกุลใน Google Workspace
     *
     * @param string $email อีเมลนักเรียน
     * @param string $firstName ชื่อจริงใหม่
     * @param string $lastName นามสกุลใหม่
     * @return array ผลลัพธ์จาก API
     */
    public function updateName($email, $firstName, $lastName) {
        $payload = [
            "token" => $this->secretToken,
            "action" => "updateName",
            "email" => $email,
            "firstName" => $firstName,
            "lastName" => $lastName
        ];

        return $this->sendRequest($payload);
    }

    /**
     * ดึงข้อมูลบัญชี (ตัวอย่าง)
     */
    public function getUserInfo($email) {
        $payload = [
            "token" => $this->secretToken,
            "action" => "getUserInfo",
            "email" => $email
        ];

        return $this->sendRequest($payload);
    }

    /**
     * ส่ง cURL Request ไปยัง GAS
     */
    private function sendRequest($payload) {
        $ch = curl_init($this->gasUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // GAS จะ Redirect 302 เสมอ
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($error) {
            return ["status" => "error", "message" => "cURL Error: " . $error];
        }

        // ตรวจสอบว่าได้ผลลัพธ์เป็น JSON หรือไม่
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === null) {
            return [
                "status" => "error", 
                "message" => "Invalid response from server. HTTP Code: " . $httpCode,
                "raw_response" => $response
            ];
        }
        
        return $decodedResponse;
    }
}
