<?php
$gasUrl = "https://script.google.com/macros/s/AKfycbw2TpxxBNgBkfyJeDQlyTMwn8LAIn8LKN8ygIfyWXB4cNAAQ80gXK-BjvZzbQZweozR/exec"; 
$secretToken = "stdcare_phichai_secret_token_1234";

function sendRequest($gasUrl, $payload) {
    $ch = curl_init($gasUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ["status" => "error", "message" => "cURL Error: " . $error];
    }
    return json_decode($response, true) ?: ["raw" => $response];
}

echo "Testing getUserInfo with valid email:\n";
$res1 = sendRequest($gasUrl, [
    "token" => $secretToken,
    "action" => "getUserInfo",
    "email" => "ronachai@phichai.ac.th"
]);
print_r($res1);
echo "----------------------------------------\n";

echo "Testing getUserInfo with wildcard:\n";
$res2 = sendRequest($gasUrl, [
    "token" => $secretToken,
    "action" => "getUserInfo",
    "email" => "*"
]);
print_r($res2);
echo "----------------------------------------\n";

echo "Testing getUserInfo with empty email:\n";
$res3 = sendRequest($gasUrl, [
    "token" => $secretToken,
    "action" => "getUserInfo",
    "email" => ""
]);
print_r($res3);
echo "----------------------------------------\n";
?>
