<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Check authentication
if (!isset($_SESSION['Teacher_login'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once "../../config/Database.php";

function callGeminiAPI($apiKey, $model, $prompt, $isJson = false) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $apiKey;
    $data = [
        'contents' => [['parts' => [['text' => $prompt]]]]
    ];
    if ($isJson) {
        $data['generationConfig'] = ['responseMimeType' => 'application/json'];
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
        'success' => ($response !== false && $httpCode === 200),
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $curlError
    ];
}

$session_user = $_SESSION['Teacher_login'] ?? '';

try {
    $connectDB = new Database("phichaia_student");
    $pdo = $connectDB->getConnection();

    // Look up real Teach_id using the session user value (could be Teach_id or Teach_name)
    $stmt = $pdo->prepare("SELECT * FROM teacher WHERE (Teach_id = ? OR Teach_name = ?) AND Teach_status = '1' LIMIT 1");
    $stmt->execute([$session_user, $session_user]);
    $teacherData = $stmt->fetch();
    
    if (!$teacherData) {
        echo json_encode(['success' => false, 'error' => 'ไม่พบข้อมูลอาจารย์ผู้สอนในระบบ']);
        exit;
    }
    
    $teacher_id = $teacherData['Teach_id'];

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'get_key':
            $stmt = $pdo->prepare("SELECT gemini_api_key FROM teacher WHERE Teach_id = ?");
            $stmt->execute([$teacher_id]);
            $teacher = $stmt->fetch();
            $key = $teacher['gemini_api_key'] ?? '';
            
            // Mask key for safety (show only first 4 and last 4 characters if not empty)
            $maskedKey = '';
            if (!empty($key)) {
                $len = strlen($key);
                if ($len > 8) {
                    $maskedKey = substr($key, 0, 4) . str_repeat('*', $len - 8) . substr($key, -4);
                } else {
                    $maskedKey = str_repeat('*', $len);
                }
            }
            echo json_encode(['success' => true, 'has_key' => !empty($key), 'masked_key' => $maskedKey]);
            break;

        case 'save_key':
            $input = json_decode(file_get_contents('php://input'), true);
            $key = trim($input['gemini_api_key'] ?? '');
            
            $stmt = $pdo->prepare("UPDATE teacher SET gemini_api_key = ? WHERE Teach_id = ?");
            $result = $stmt->execute([empty($key) ? null : $key, $teacher_id]);
            
            echo json_encode(['success' => $result]);
            break;

        case 'generate_homeroom':
            // Get teacher's key first
            $stmt = $pdo->prepare("SELECT gemini_api_key FROM teacher WHERE Teach_id = ?");
            $stmt->execute([$teacher_id]);
            $teacher = $stmt->fetch();
            $apiKey = trim($teacher['gemini_api_key'] ?? '');

            if (empty($apiKey)) {
                echo json_encode(['success' => false, 'needs_key' => true, 'error' => 'กรุณาตั้งค่า Gemini API Key ก่อนใช้งาน']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $topic = trim($input['topic'] ?? '');
            $typeName = trim($input['type_name'] ?? '');

            if (empty($topic)) {
                echo json_encode(['success' => false, 'error' => 'กรุณากรอกหัวข้อเรื่องเพื่อใช้ในการวางแผนกิจกรรม']);
                exit;
            }

            $prompt = "คุณคือผู้ช่วยครูแนะแนวและครูที่ปรึกษาในการบันทึกกิจกรรมโฮมรูมระดับมัธยมศึกษา\n"
                    . "หัวข้อกิจกรรมคือ: \"{$topic}\"\n"
                    . "ประเภทกิจกรรม: \"{$typeName}\"\n\n"
                    . "กรุณาช่วยวางแผนและเขียนเนื้อหากิจกรรมโฮมรูมนี้ โดยส่งผลลัพธ์เป็น JSON ภาษาไทยที่มีรูปแบบดังนี้เท่านั้น (ห้ามมีคำนำเกริ่นนำหรือเครื่องหมายคำพูด Markdown คลุม ให้ส่งเฉพาะเนื้อหา JSON ดิบๆ เลย):\n"
                    . "{\n"
                    . "  \"detail\": \"รายละเอียดกิจกรรม (ระบุขั้นตอนการทำกิจกรรม ขั้นนำ ขั้นดำเนินการ และขั้นสรุป สั้นๆ กระชับ และปฏิบัติได้จริง ประมาณ 3-4 ประโยค)\",\n"
                    . "  \"result\": \"ผลที่คาดว่าจะได้รับ (ระบุผลลัพธ์ที่นักเรียนจะได้รับหลังจากทำกิจกรรมเสร็จสิ้น กระชับและวัดผลได้ 1-2 ข้อ)\"\n"
                    . "}";

            $res = callGeminiAPI($apiKey, 'gemini-2.5-flash', $prompt, true);
            if (!$res['success']) {
                $errData = json_decode($res['response'], true);
                $msg = $errData['error']['message'] ?? '';
                $isTransient = ($res['http_code'] === 429 || $res['http_code'] >= 500 || stripos($msg, 'demand') !== false || stripos($msg, 'limit') !== false || stripos($msg, 'overloaded') !== false || stripos($msg, 'quota') !== false);
                if ($isTransient) {
                    $res = callGeminiAPI($apiKey, 'gemini-1.5-flash', $prompt, true);
                }
            }

            if (!$res['success']) {
                $errData = json_decode($res['response'], true);
                $msg = $errData['error']['message'] ?? ($res['error'] ? 'การเชื่อมต่อล้มเหลว: ' . $res['error'] : 'Gemini API Error (HTTP ' . $res['http_code'] . ')');
                echo json_encode(['success' => false, 'error' => $msg]);
                exit;
            }

            $response = $res['response'];

            $resDecoded = json_decode($response, true);
            $textResult = $resDecoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($textResult)) {
                echo json_encode(['success' => false, 'error' => 'ไม่ได้รับการตอบกลับจาก AI']);
                exit;
            }

            $aiData = json_decode(trim($textResult), true);
            if (!$aiData) {
                if (preg_match('/\{.*\}/s', $textResult, $matches)) {
                    $aiData = json_decode($matches[0], true);
                }
            }

            if (!$aiData) {
                echo json_encode(['success' => false, 'error' => 'ข้อมูลที่ได้รับจาก AI ไม่สอดคล้องกับรูปแบบที่กำหนด', 'raw' => $textResult]);
                exit;
            }

            echo json_encode(['success' => true, 'data' => $aiData]);
            break;

        case 'generate_visithome':
            // Get teacher's key first
            $stmt = $pdo->prepare("SELECT gemini_api_key FROM teacher WHERE Teach_id = ?");
            $stmt->execute([$teacher_id]);
            $teacher = $stmt->fetch();
            $apiKey = trim($teacher['gemini_api_key'] ?? '');

            if (empty($apiKey)) {
                echo json_encode(['success' => false, 'needs_key' => true, 'error' => 'กรุณาตั้งค่า Gemini API Key ก่อนใช้งาน']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $studentName = trim($input['student_name'] ?? '');
            $answers = $input['answers'] ?? [];

            if (empty($answers)) {
                echo json_encode(['success' => false, 'error' => 'ไม่พบข้อมูลการประเมินเพื่อใช้ในการวิเคราะห์']);
                exit;
            }

            // Map standard questions
            $questions = [
                1 => "บ้านที่อยู่อาศัย",
                2 => "ระยะทางระหว่างบ้านกับโรงเรียน",
                3 => "การเดินทางไปโรงเรียนของนักเรียน",
                4 => "สภาพแวดล้อมของบ้าน",
                5 => "อาชีพของผู้ปกครอง",
                6 => "สถานที่ทำงานของบิดามารดา",
                7 => "สถานภาพของบิดามารดา",
                8 => "วิธีการที่ผู้ปกครองอบรมเลี้ยงดูนักเรียน",
                9 => "โรคประจำตัวของนักเรียน",
                10 => "ความสัมพันธ์ของสมาชิกในครอบครัว",
                11 => "หน้าที่รับผิดชอบภายในบ้าน",
                12 => "สมาชิกในครอบครัวนักเรียนสนิทสนมกับใครมากที่สุด",
                13 => "รายได้กับการใช้จ่ายในครอบครัว",
                14 => "ลักษณะเพื่อนเล่นที่บ้านของนักเรียนโดยปกติเป็น",
                15 => "ความต้องการของผู้ปกครองเมื่อนักเรียนจบชั้นสูงสุด",
                16 => "เมื่อนักเรียนมีปัญหา นักเรียนจะปรึกษาใคร",
                17 => "ความรู้สึกของผู้ปกครองที่มีต่อครูที่มาเยี่ยมบ้าน",
                18 => "ทัศนคติ/ความรู้สึกของผู้ปกครองที่มีต่อโรงเรียน"
            ];

            $answersText = "";
            foreach ($answers as $qNum => $ansVal) {
                if (isset($questions[$qNum])) {
                    $answersText .= "- " . $questions[$qNum] . ": " . $ansVal . "\n";
                }
            }

            $prompt = "คุณคือผู้ช่วยครูที่ปรึกษาในการวิเคราะห์ข้อมูลจากการเยี่ยมบ้านนักเรียนระดับมัธยมศึกษา\n"
                    . "ชื่อนักเรียน: \"{$studentName}\"\n"
                    . "ข้อมูลจากการสังเกตและประเมินในการเยี่ยมบ้าน (ข้อ 1 - 18):\n"
                    . "{$answersText}\n\n"
                    . "กรุณาช่วยเขียนสรุปในหัวข้อ \"ปัญหา อุปสรรค และความต้องการความช่วยเหลือ\" (สำหรับบันทึกในแบบรายงานเยี่ยมบ้าน) "
                    . "โดยวิเคราะห์จากสภาพแวดล้อม สภาพครอบครัว และความขัดสนทางการเงินข้างต้น เขียนให้ออกมาเป็นร้อยแก้วภาษาไทยเชิงวิเคราะห์ที่เป็นทางการ สุภาพ และสร้างสรรค์ "
                    . "ความยาวประมาณ 3-5 ประโยค พร้อมเสนอแนะแนวทางแก้ไขหรือความช่วยเหลือที่สอดคล้องกับสถานการณ์ของนักเรียน (เช่น เสนอแนะให้ทุนการศึกษา, ดูแลใกล้ชิดร่วมกับผู้ปกครอง หรือประสานครูแนะแนว)\n"
                    . "(ห้ามมีคำนำเกริ่นนำหรือเครื่องหมายคำพูด Markdown คลุม ให้ส่งเฉพาะเนื้อหาข้อความสรุปรายงานเท่านั้น)";

            $res = callGeminiAPI($apiKey, 'gemini-2.5-flash', $prompt, false);
            if (!$res['success']) {
                $errData = json_decode($res['response'], true);
                $msg = $errData['error']['message'] ?? '';
                $isTransient = ($res['http_code'] === 429 || $res['http_code'] >= 500 || stripos($msg, 'demand') !== false || stripos($msg, 'limit') !== false || stripos($msg, 'overloaded') !== false || stripos($msg, 'quota') !== false);
                if ($isTransient) {
                    $res = callGeminiAPI($apiKey, 'gemini-1.5-flash', $prompt, false);
                }
            }

            if (!$res['success']) {
                $errData = json_decode($res['response'], true);
                $msg = $errData['error']['message'] ?? ($res['error'] ? 'การเชื่อมต่อล้มเหลว: ' . $res['error'] : 'Gemini API Error (HTTP ' . $res['http_code'] . ')');
                echo json_encode(['success' => false, 'error' => $msg]);
                exit;
            }

            $response = $res['response'];

            $resDecoded = json_decode($response, true);
            $textResult = $resDecoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($textResult)) {
                echo json_encode(['success' => false, 'error' => 'ไม่ได้รับการตอบกลับจาก AI']);
                exit;
            }

            echo json_encode(['success' => true, 'summary' => trim($textResult)]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
}
