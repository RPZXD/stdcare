<?php
require_once __DIR__ . '/../controllers/GoogleWorkspaceController.php';
$controller = new Controllers\GoogleWorkspaceController();

echo "Testing listUsers API action on the new GAS Web App URL:\n";
$res = $controller->listUsers();
if (isset($res['status']) && $res['status'] === 'success') {
    echo "Connection Successful! Fetched " . count($res['users']) . " users.\n";
    echo "First 5 users:\n";
    print_r(array_slice($res['users'], 0, 5));
} else {
    echo "Error calling API:\n";
    print_r($res);
}
?>
