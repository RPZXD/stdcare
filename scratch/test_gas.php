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

$actions = ["list_users", "get_all_users", "get_users_list", "list_all_users", "list_user", "get_user_list", "listAllDirectoryUsers", "listDomainUsers"];
foreach ($actions as $act) {
    echo "Testing action: $act...\n";
    $res = sendRequest($gasUrl, [
        "token" => $secretToken,
        "action" => $act
    ]);
    if (isset($res['status']) && $res['status'] !== 'error') {
        echo "Success with action $act! Response keys: " . implode(', ', array_keys($res)) . "\n";
        print_r(array_slice($res, 0, 5));
    } else {
        echo "Failed with action $act: " . ($res['message'] ?? (isset($res['raw']) ? substr($res['raw'], 0, 100) : 'unknown')) . "\n";
    }
    echo "----------------------------------------\n";
}
?>
