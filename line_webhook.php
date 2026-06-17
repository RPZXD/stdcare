<?php
declare(strict_types=1);

/**
 * LINE Messaging API Webhook Receiver
 * Handles incoming webhooks, validates signature, maps parent LINE User IDs to Student IDs,
 * replies to users, and logs events for the monitor dashboard.
 * Refactored to comply with php-pro guidelines.
 */

header("Content-Type: application/json");

// 1. Read Raw Payload and Headers
$raw_body = (string)file_get_contents('php://input');
$headers = getallheaders();

// Normalize headers for cross-platform robustness
$signature_header = '';
foreach ($headers as $key => $val) {
    if (strtolower((string)$key) === 'x-line-signature') {
        $signature_header = (string)$val;
        break;
    }
}

// 2. Initialize DB Connection and Models
require_once __DIR__ . '/classes/DatabaseUsers.php';
require_once __DIR__ . '/models/SettingModel.php';

try {
    $db_connection = new App\DatabaseUsers();
    $db = $db_connection->getPDO();
    
    // Auto-run migrations if tables/columns are missing
    checkAndRunMigrations($db);

    $settingsModel = new App\Models\SettingModel($db);
    $settings = $settingsModel->getAllTimeSettings();
} catch (Exception $e) {
    error_log("LINE Webhook: DB Connection Failed - " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$channel_access_token = (string)($settings['line_channel_access_token'] ?? '');
$channel_secret = (string)($settings['line_channel_secret'] ?? '');

// 3. Decode Payload
try {
    $data = json_decode($raw_body, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    logWebhookEvent($db, 'json_parse_error', null, $raw_body, $headers, 'failed', 'Invalid JSON structure: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload JSON"]);
    exit;
}

if (!is_array($data) || !isset($data['events']) || !is_array($data['events'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid payload structure"]);
    exit;
}

// 4. Verify Signature (if Channel Secret is configured)
if (!empty($channel_secret)) {
    $calculated_hash = base64_encode(hash_hmac('sha256', $raw_body, $channel_secret, true));
    if ($calculated_hash !== $signature_header) {
        logWebhookEvent($db, 'signature_failed', null, $raw_body, $headers, 'signature_failed', 'Signature verification failed.');
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid signature"]);
        exit;
    }
}

// 5. Process Events
$response_logs = [];
foreach ($data['events'] as $event) {
    if (!is_array($event)) {
        continue;
    }
    
    $event_type = (string)($event['type'] ?? '');
    $user_id = isset($event['source']['userId']) ? (string)$event['source']['userId'] : null;
    $reply_token = isset($event['replyToken']) ? (string)$event['replyToken'] : null;

    $status = 'success';
    $response_msg = '';

    if ($event_type === 'message' && isset($event['message']['text'])) {
        $text = trim((string)$event['message']['text']);
        
        if (strtolower($text) === '/start' || $text === 'สวัสดี' || $text === 'เริ่ม') {
            $reply_text = "สวัสดีค่ะ 🙏 ยินดีต้อนรับสู่ระบบดูแลช่วยเหลือผู้ปกครอง โรงเรียนพิชัย\n\nกรุณาส่ง *รหัสประจำตัวนักเรียน* (เช่น 27505) เพื่อลิงก์บัญชีไลน์ของคุณและรับการแจ้งเตือนเวลาเข้า-ออกเรียนของบุตรหลานค่ะ";
            sendLineReply($reply_token, $reply_text, $channel_access_token);
            $response_msg = 'Sent greeting message.';
        } else {
            // Treat the message as Student ID
            $student = $db_connection->getStudentByUsername($text);
            if ($student && is_array($student)) {
                $stu_id = (string)($student['Stu_id'] ?? $student['student_id'] ?? $text);
                $stu_name = (string)($student['Stu_pre'] ?? '') . (string)($student['Stu_name'] ?? '') . ' ' . (string)($student['Stu_sur'] ?? '');

                try {
                    // Save to parents table
                    $stmt = $db->prepare("INSERT INTO parents (line_userid, student_id, verified) 
                                          VALUES (:line, :stu, 1) 
                                          ON DUPLICATE KEY UPDATE verified = 1");
                    $stmt->execute(['line' => $user_id, 'stu' => $stu_id]);

                    $reply_text = "ยืนยันตัวตนสำเร็จ 🎉\n\nคุณได้รับการลิงก์เป็นผู้ปกครองของ: {$stu_name} (ม.{$student['Stu_major']}/{$student['Stu_room']}) เรียบร้อยแล้วค่ะ\nระบบจะส่งข้อความแจ้งเตือนเมื่อนักเรียนสแกนบัตรเข้าหรือออกเรียนนะคะ";
                    sendLineReply($reply_token, $reply_text, $channel_access_token);
                    $response_msg = "Linked parents line_userid to student_id: {$stu_id} ({$stu_name}).";
                } catch (Exception $ex) {
                    $status = 'failed';
                    $response_msg = "DB Error linking parent: " . $ex->getMessage();
                    $reply_text = "❌ เกิดข้อผิดพลาดของระบบ ไม่สามารถบันทึกข้อมูลได้ในขณะนี้ กรุณาลองใหม่อีกครั้งภายหลังค่ะ";
                    sendLineReply($reply_token, $reply_text, $channel_access_token);
                }
            } else {
                $reply_text = "❌ ไม่พบข้อมูลรหัสนักเรียน '{$text}' ในระบบ\nกรุณาตรวจสอบรหัสประจำตัวนักเรียนและส่งใหม่อีกครั้งค่ะ";
                sendLineReply($reply_token, $reply_text, $channel_access_token);
                $response_msg = "Student ID '{$text}' not found in DB.";
            }
        }
    } else if ($event_type === 'join') {
        // Invited to group/room
        $group_id = (string)($event['source']['groupId'] ?? $event['source']['roomId'] ?? '');
        $reply_text = "สวัสดีค่ะ 🙏 บอทระบบดูแลช่วยเหลือนักเรียน (StdCare) ได้เข้าร่วมกลุ่มเรียบร้อยแล้วค่ะ\n\n🔑 LINE Group ID สำหรับกลุ่มนี้คือ:\n`{$group_id}`\n\nคุณสามารถนำรหัส Group ID นี้ไปกรอกในระบบตั้งค่าห้องเรียนเพื่อส่งข้อความสรุปรายวัน หรือใช้สำหรับเชื่อมต่อกับระบบอื่นที่ต้องการได้ค่ะ";
        sendLineReply($reply_token, $reply_text, $channel_access_token);
        $response_msg = "Joined group/room. Provided group ID: {$group_id}";
    } else {
        $response_msg = "Unhandled event type: {$event_type}";
    }

    logWebhookEvent($db, $event_type, $user_id, $raw_body, $headers, $status, $response_msg);
    $response_logs[] = ["event" => $event_type, "status" => $status, "message" => $response_msg];
}

// 6. Return response
echo json_encode(["status" => "success", "processed_events" => $response_logs]);

/**
 * Log webhook event to database
 * Uses direct dependency injection of PDO for type safety and code quality.
 */
function logWebhookEvent(PDO $db, string $eventType, ?string $userId, string $payload, array $headers, string $status, string $responseMessage): void 
{
    try {
        $stmt = $db->prepare("INSERT INTO line_webhook_logs (event_type, user_id, payload, headers, status, response_message) 
                              VALUES (:type, :user, :payload, :headers, :status, :resp)");
        $stmt->execute([
            'type' => $eventType,
            'user' => $userId,
            'payload' => $payload,
            'headers' => json_encode($headers, JSON_THROW_ON_ERROR),
            'status' => $status,
            'resp' => $responseMessage
        ]);
    } catch (Exception $e) {
        error_log("LINE Webhook: Failed to write log - " . $e->getMessage());
    }
}

/**
 * Send reply message using LINE Messaging API
 */
function sendLineReply(?string $replyToken, string $text, string $accessToken): bool 
{
    if (empty($accessToken) || empty($replyToken)) {
        return false;
    }

    $url = 'https://api.line.me/v2/bot/message/reply';
    $data = [
        'replyToken' => $replyToken,
        'messages' => [
            [
                'type' => 'text',
                'text' => $text
            ]
        ]
    ];

    try {
        $payload = json_encode($data, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        error_log("LINE Webhook: Failed to encode reply JSON - " . $e->getMessage());
        return false;
    }

    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer " . $accessToken . "\r\n",
            'method' => 'POST',
            'content' => $payload,
            'ignore_errors' => true
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result !== false;
}

/**
 * Automatically ensures database tables and columns are created.
 */
function checkAndRunMigrations(PDO $db): void {
    // 1. Add line_userid column to parents table if missing
    try {
        $db->query("SELECT line_userid FROM parents LIMIT 1");
    } catch (PDOException $e) {
        try {
            $columns = $db->query("DESCRIBE `parents`")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('line_userid', $columns)) {
                $after = in_array('telegram_id', $columns) ? " AFTER `telegram_id`" : "";
                $db->query("ALTER TABLE `parents` ADD COLUMN `line_userid` VARCHAR(100) NULL{$after}");
                $db->query("ALTER TABLE `parents` ADD UNIQUE KEY `line_stu` (`line_userid`, `student_id`)");
            }
        } catch (Exception $ex) {
            error_log("LINE Auto-Migration line_userid Failed: " . $ex->getMessage());
        }
    }

    // 2. Create line_webhook_logs table if missing
    try {
        $db->query("SELECT 1 FROM line_webhook_logs LIMIT 1");
    } catch (PDOException $e) {
        try {
            $db->query("CREATE TABLE IF NOT EXISTS `line_webhook_logs` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `event_type` VARCHAR(50) NULL,
                `user_id` VARCHAR(100) NULL,
                `payload` LONGTEXT NOT NULL,
                `headers` TEXT NULL,
                `status` VARCHAR(50) DEFAULT 'pending',
                `response_message` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (Exception $ex) {
            error_log("LINE Auto-Migration line_webhook_logs Failed: " . $ex->getMessage());
        }
    }

    // 3. Create linetoken table if missing
    try {
        $db->query("SELECT 1 FROM linetoken LIMIT 1");
    } catch (PDOException $e) {
        try {
            $db->query("CREATE TABLE IF NOT EXISTS `linetoken` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `line_name` VARCHAR(200) NOT NULL,
                `line_class` INT NOT NULL,
                `line_room` INT NOT NULL DEFAULT 0,
                `token` VARCHAR(250) NOT NULL,
                `create_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } catch (Exception $ex) {
            error_log("LINE Auto-Migration linetoken Failed: " . $ex->getMessage());
        }
    }

    // 4. Ensure line_channel_secret key in settings table
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM time_settings WHERE setting_key = 'line_channel_secret'");
        if ($stmt->fetchColumn() === 0) {
            $db->query("INSERT INTO time_settings (setting_key, setting_value) VALUES ('line_channel_secret', '')");
        }
    } catch (PDOException $e) {
        error_log("LINE Auto-Migration setting key Failed: " . $e->getMessage());
    }
}
