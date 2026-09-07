<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = ['http://localhost:5173', 'http://localhost:5174', 'http://localhost:5175'];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

session_start();

if (isset($_SESSION["user_id"])) {
    echo json_encode([
        "authenticated" => true,
        "user" => [
            "id" => $_SESSION["user_id"],
            "name" => $_SESSION["user_name"],
            "email" => $_SESSION["user_email"],
            "role" => $_SESSION["user_role"]
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        "authenticated" => false,
        "message" => "لا توجد جلسة نشطة"
    ]);
}
?>
