<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['http://localhost:5173', 'http://localhost:5174', 'http://localhost:5175'];

if (in_array($origin, $allowed)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "غير مصرح"]);
    exit;
}

require_once "../config.php";

$method = $_SERVER['REQUEST_METHOD'];

// 1. جلب الطلاب
if ($method === 'GET') {
    $sql = "SELECT id, name, email, role FROM users WHERE role = 'student' ORDER BY id DESC";
    $result = $conn->query($sql);
    $students = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    echo json_encode(["success" => true, "students" => $students]);
    exit;
}

// 2. إضافة طالب جديد
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $password = $input["password"] ?? "123456";

    if ($name === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "الاسم والبريد مطلوبان"]);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = 'student';

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تمت الإضافة بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "البريد مسجل مسبقاً"]);
    }
    $stmt->close();
    exit;
}

// 3. تعديل بيانات طالب
if ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = intval($input["id"] ?? 0);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $password = trim($input["password"] ?? "");

    if ($id <= 0 || $name === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "البيانات غير مكتملة"]);
        exit;
    }

    if ($password !== "") {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ? AND role = 'student'");
        $stmt->bind_param("sssi", $name, $email, $hashed, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND role = 'student'");
        $stmt->bind_param("ssi", $name, $email, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث بيانات الطالب بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل التحديث أو أن البريد مستخدم"]);
    }
    $stmt->close();
    exit;
}

// 4. حذف طالب
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "تم الحذف"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "لم يتم العثور على السجل"]);
    }
    $stmt->close();
    exit;
}

$conn->close();
?>
