<?php
// السماح بالمنافذ المحلية التابعة لـ Vite ودعم تبادل الجلسات
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = [
    'http://localhost:5173',
    'http://localhost:5174',
    'http://localhost:5175'
];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// معالجة طلب الفحص المسبق من المتصفح (Preflight Request)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
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

/* التأكد أن الطلب POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "طريقة الطلب غير صحيحة"
    ]);
    exit;
}

/* استقبال البيانات بصيغة JSON القادمة من الواجهة */
$input_data = json_decode(file_get_contents("php://input"), true);

$email = trim($input_data["email"] ?? $_POST["email"] ?? "");
$password = $input_data["password"] ?? $_POST["password"] ?? "";

/* التحقق من وجود المدخلات */
if ($email === "" || $password === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "يرجى إدخال البريد الإلكتروني وكلمة المرور"
    ]);
    exit;
}

/* البحث عن المستخدم */
$stmt = $conn->prepare(
    "SELECT id, name, email, password, role
     FROM users
     WHERE email = ?
     LIMIT 1"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

/* فحص وجود الحساب */
if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"
    ]);
    exit;
}

$user = $result->fetch_assoc();

/* التحقق من كلمة المرور المشفرة */
if (!password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"
    ]);
    exit;
}

/* حذف كلمة المرور من بيانات الاستجابة */
unset($user["password"]);

/* تجديد معرف الجلسة وحفظ البيانات */
session_regenerate_id(true);
$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["name"];
$_SESSION["user_email"] = $user["email"];
$_SESSION["user_role"] = $user["role"];

/* إرسال استجابة JSON للواجهة */
echo json_encode([
    "success" => true,
    "message" => "تم تسجيل الدخول بنجاح",
    "user" => $user
]);

$stmt->close();
$conn->close();
?>
