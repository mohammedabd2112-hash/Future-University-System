<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['http://localhost:5173', 'http://localhost:5174', 'http://localhost:5175'];

if (in_array($origin, $allowed)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();
require_once "../config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "طريقة الطلب غير صحيحة"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$email = trim($input["email"] ?? $_POST["email"] ?? "");
$password = $input["password"] ?? $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "يرجى إدخال البريد الإلكتروني وكلمة المرور"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"]);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"]);
    exit;
}

unset($user["password"]);
session_regenerate_id(true);
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];

echo json_encode([
    "success" => true,
    "message" => "تم تسجيل الدخول بنجاح",
    "user" => $user
]);

$stmt->close();
$conn->close();
?>
