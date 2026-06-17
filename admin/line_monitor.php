<?php
declare(strict_types=1);

/**
 * Controller: LINE Webhook Monitor & Token Management
 * MVC Pattern - Handles admin interactions, token CRUD, and webhook testing/simulation
 * Refactored to follow php-pro guidelines.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');

require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../class/UserLogin.php';
require_once __DIR__ . '/../class/Utils.php';
require_once __DIR__ . '/../models/SettingModel.php';

use App\DatabaseUsers;
use App\Models\SettingModel;

// 1. Check Login Permission
if (!isset($_SESSION['Admin_login'])) {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

try {
    $connectDB = new DatabaseUsers();
    $db = $connectDB->getPDO();
} catch (Exception $e) {
    error_log("LINE Monitor: Database connection failed - " . $e->getMessage());
    die("Database connection failed. Please contact administrator.");
}

// Auto-run migrations if tables/columns are missing
checkAndRunMigrations($db);

$user = new UserLogin($db);

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
$userid = (string)$_SESSION['Admin_login'];
$userData = $user->userData($userid);
$_SESSION['admin_data'] = $userData;

$settingsModel = new SettingModel($db);
$timeSettings = $settingsModel->getAllTimeSettings();
$channel_secret = (string)($timeSettings['line_channel_secret'] ?? '');
$channel_access_token = (string)($timeSettings['line_channel_access_token'] ?? '');

// 2. Process Actions
$action = (string)($_GET['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'clear_logs') {
        try {
            $db->query("TRUNCATE TABLE line_webhook_logs");
            $_SESSION['flash_message'] = "ล้างประวัติการทำงานเรียบร้อยแล้ว";
            $_SESSION['flash_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
        header("Location: line_monitor.php");
        exit;
    }

    if ($action === 'unlink_parent') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM parents WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['flash_message'] = "ยกเลิกการเชื่อมต่อผู้ปกครองเรียบร้อยแล้ว";
            $_SESSION['flash_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
        header("Location: line_monitor.php?tab=parents");
        exit;
    }

    if ($action === 'add_token') {
        $line_name = trim((string)($_POST['line_name'] ?? ''));
        $line_class = (int)($_POST['line_class'] ?? 0);
        $line_room = (int)($_POST['line_room'] ?? 0);
        $token = trim((string)($_POST['token'] ?? ''));

        try {
            $stmt = $db->prepare("INSERT INTO linetoken (line_name, line_class, line_room, token) VALUES (:name, :class, :room, :token)");
            $stmt->execute(['name' => $line_name, 'class' => $line_class, 'room' => $line_room, 'token' => $token]);
            $_SESSION['flash_message'] = "เพิ่มกลุ่ม LINE Notify เรียบร้อยแล้ว";
            $_SESSION['flash_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
        header("Location: line_monitor.php?tab=tokens");
        exit;
    }

    if ($action === 'edit_token') {
        $id = (int)($_POST['id'] ?? 0);
        $line_name = trim((string)($_POST['line_name'] ?? ''));
        $line_class = (int)($_POST['line_class'] ?? 0);
        $line_room = (int)($_POST['line_room'] ?? 0);
        $token = trim((string)($_POST['token'] ?? ''));

        try {
            $stmt = $db->prepare("UPDATE linetoken SET line_name = :name, line_class = :class, line_room = :room, token = :token WHERE id = :id");
            $stmt->execute(['name' => $line_name, 'class' => $line_class, 'room' => $line_room, 'token' => $token, 'id' => $id]);
            $_SESSION['flash_message'] = "แก้ไขกลุ่ม LINE Notify เรียบร้อยแล้ว";
            $_SESSION['flash_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
        header("Location: line_monitor.php?tab=tokens");
        exit;
    }

    if ($action === 'delete_token') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $db->prepare("DELETE FROM linetoken WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $_SESSION['flash_message'] = "ลบกลุ่ม LINE Notify เรียบร้อยแล้ว";
            $_SESSION['flash_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['flash_message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            $_SESSION['flash_type'] = "error";
        }
        header("Location: line_monitor.php?tab=tokens");
        exit;
    }

    if ($action === 'test_notify') {
        $id = (int)($_POST['id'] ?? 0);
        $test_message = trim((string)($_POST['test_message'] ?? 'ทดสอบการส่งการแจ้งเตือนจากระบบ StdCare'));
        header('Content-Type: application/json');

        try {
            $stmt = $db->prepare("SELECT token FROM linetoken WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $token = (string)$stmt->fetchColumn();

            if (empty($token)) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบ Token ในระบบ'], JSON_THROW_ON_ERROR);
                exit;
            }

            // Execute LINE Notify Curl Request
            $ch = curl_init('https://notify-api.line.me/api/notify');
            if ($ch === false) {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถเริ่มต้นระบบ cURL ได้'], JSON_THROW_ON_ERROR);
                exit;
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['message' => $test_message]));
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            $res = curl_exec($ch);
            $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($res === false) {
                echo json_encode(['success' => false, 'message' => 'การเชื่อมต่อล้มเหลว'], JSON_THROW_ON_ERROR);
                exit;
            }

            $res_dec = json_decode((string)$res, true, 512, JSON_THROW_ON_ERROR);
            if ($http_code === 200 && ($res_dec['status'] ?? 0) === 200) {
                echo json_encode(['success' => true, 'message' => 'ส่งข้อความสำเร็จ'], JSON_THROW_ON_ERROR);
            } else {
                echo json_encode(['success' => false, 'message' => 'LINE Notify API Error: ' . ($res_dec['message'] ?? $res)], JSON_THROW_ON_ERROR);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'simulate') {
        header('Content-Type: application/json');
        $sim_user_id = trim((string)($_POST['sim_user_id'] ?? 'U_SIMULATED_PARENT_USER'));
        $sim_text = trim((string)($_POST['sim_text'] ?? ''));

        if (empty($sim_text)) {
            echo json_encode(['success' => false, 'message' => 'กรุณากรอกรหัสนักเรียนสำหรับการทดสอบ'], JSON_THROW_ON_ERROR);
            exit;
        }

        // Build mock LINE Webhook event payload
        $mock_payload = [
            "destination" => "U1234567890abcdef1234567890abcdef",
            "events" => [
                [
                    "type" => "message",
                    "message" => [
                        "type" => "text",
                        "id" => "sim_msg_" . uniqid(),
                        "text" => $sim_text
                    ],
                    "timestamp" => time() * 1000,
                    "source" => [
                        "type" => "user",
                        "userId" => $sim_user_id
                    ],
                    "replyToken" => "sim_reply_" . uniqid()
                ]
            ]
        ];

        $payload_str = json_encode($mock_payload);

        // Resolve absolute URL to line_webhook.php
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = (string)$_SERVER['HTTP_HOST'];
        $dir = (string)dirname((string)$_SERVER['SCRIPT_NAME']);
        $dir = ($dir === '\\' || $dir === '/') ? '' : $dir;
        $parent_dir = (string)dirname($dir);
        $parent_dir = ($parent_dir === '\\' || $parent_dir === '/') ? '' : $parent_dir;
        $webhook_url = $protocol . '://' . $host . $parent_dir . '/line_webhook.php';

        $curl_headers = [
            "Content-Type: application/json",
            "User-Agent: StdCare-Webhook-Simulator"
        ];

        if (!empty($channel_secret)) {
            $sig = base64_encode(hash_hmac('sha256', $payload_str, $channel_secret, true));
            $curl_headers[] = "X-Line-Signature: " . $sig;
        }

        $ch = curl_init($webhook_url);
        if ($ch === false) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างเซสชัน cURL ได้'], JSON_THROW_ON_ERROR);
            exit;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_str);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            echo json_encode([
                'success' => false,
                'message' => 'cURL Error calling local webhook: ' . $curl_err,
                'url' => $webhook_url
            ], JSON_THROW_ON_ERROR);
        } else {
            echo json_encode([
                'success' => $http_code === 200,
                'http_code' => $http_code,
                'webhook_url' => $webhook_url,
                'payload' => $mock_payload,
                'response' => json_decode((string)$response, true) ?? $response
            ], JSON_THROW_ON_ERROR);
        }
        exit;
    }
}

// 3. Fetch Data for default view
$activePage = 'line_monitor';
$pageTitle = 'LINE Webhook & Notify Monitor';

// Stats & data initialization
$stats = [
    'total_logs' => 0,
    'err_logs' => 0,
    'linked_parents' => 0,
    'notify_tokens' => 0
];
$logs = [];
$tokens = [];
$linked_parents = [];
$db_error = null;

try {
    $stats['total_logs'] = (int)$db->query("SELECT COUNT(*) FROM line_webhook_logs")->fetchColumn();
    $stats['err_logs'] = (int)$db->query("SELECT COUNT(*) FROM line_webhook_logs WHERE status != 'success'")->fetchColumn();
    $logs = $db->query("SELECT * FROM line_webhook_logs ORDER BY id DESC LIMIT 50")->fetchAll();
} catch (PDOException $e) {
    $db_error = "ตาราง line_webhook_logs: " . $e->getMessage();
}

try {
    $stats['linked_parents'] = (int)$db->query("SELECT COUNT(DISTINCT line_userid) FROM parents WHERE line_userid IS NOT NULL")->fetchColumn();
    $linked_parents = $db->query("
        SELECT p.id, p.line_userid, p.student_id, p.created_at, 
               s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room
        FROM parents p
        LEFT JOIN student s ON p.student_id = s.Stu_id
        WHERE p.line_userid IS NOT NULL
        ORDER BY p.id DESC
    ")->fetchAll();
} catch (PDOException $e) {
    if (!$db_error) {
        $db_error = "ตาราง parents / student: " . $e->getMessage();
    }
}

try {
    $stats['notify_tokens'] = (int)$db->query("SELECT COUNT(*) FROM linetoken")->fetchColumn();
    $tokens = $db->query("SELECT * FROM linetoken ORDER BY line_class ASC, line_room ASC")->fetchAll();
} catch (PDOException $e) {
    if (!$db_error) {
        $db_error = "ตาราง linetoken: " . $e->getMessage();
    }
}

// 4. Render View
include __DIR__ . '/../views/admin/line_monitor.php';
