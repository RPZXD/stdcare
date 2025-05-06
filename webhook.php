<?php
// รับ input JSON
$json = file_get_contents('php://input');
$events = json_decode($json, true);

// เขียน Log (เพื่อดูว่า LINE ส่งข้อมูลอะไรมาบ้าง)
file_put_contents('log.txt', $json . PHP_EOL, FILE_APPEND);

// ตัวอย่าง: ตอบกลับข้อความ
if (!empty($events['events'])) {
    foreach ($events['events'] as $event) {
        if ($event['type'] == 'message') {
            $replyToken = $event['replyToken'];
            $text = $event['message']['text'];

            $messages = [
                'type' => 'text',
                'text' => 'คุณพิมพ์ว่า: ' . $text
            ];

            reply($replyToken, $messages);
        }
    }
}

function reply($replyToken, $message) {
    $access_token = '3K7fh1bhbCn0uPjgNoGQpN3jNgpwpSoMA0QaE6m4dOMJkly+SeGyDyS73+EV6wSVuLoB6M/+FwdbxRWlY6ZGuQymNTYSrFzA5xQ7AhwlwOufu+et60PnAnYK2vpyvUyy3ye0yBe7cTu+PoiFDxsmmgdB04t89/1O/w1cDnyilFU=';

    $url = 'https://api.line.me/v2/bot/message/reply';
    $data = [
        'replyToken' => $replyToken,
        'messages' => [$message]
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_exec($ch);
    curl_close($ch);
}
