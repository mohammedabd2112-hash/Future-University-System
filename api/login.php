<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once "../config.php";


/* التأكد أن الطلب POST */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "طريقة الطلب غير صحيحة"
    ]);

    exit;
}


/* استقبال البيانات */

$email = trim($_POST["email"] ?? "");

$password = $_POST["password"] ?? "";


/* التحقق من البيانات */

if ($email === "" || $password === "") {

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


/* المستخدم غير موجود */

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"
    ]);

    exit;
}


$user = $result->fetch_assoc();


/* التحقق من كلمة المرور المشفرة */

if (!password_verify($password, $user["password"])) {

    echo json_encode([
        "success" => false,
        "message" => "البريد الإلكتروني أو كلمة المرور غير صحيحة"
    ]);

    exit;
}


/* حذف كلمة المرور من البيانات التي سترسل للمتصفح */

unset($user["password"]);


/* إنشاء Session */
/* إنشاء Session جديدة وآمنة */

session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];

$_SESSION["user_name"] = $user["name"];

$_SESSION["user_email"] = $user["email"];

$_SESSION["user_role"] = $user["role"];


/* إرسال النتيجة */

echo json_encode([

    "success" => true,

    "message" => "تم تسجيل الدخول بنجاح",

    "user" => $user

]);


$stmt->close();

$conn->close();

?>