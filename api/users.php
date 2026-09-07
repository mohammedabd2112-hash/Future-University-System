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

if ($_SESSION["user_role"] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "صلاحيات مسؤول مطلوبة"]);
    exit;
}

require_once "../config.php";

$method = $_SERVER['REQUEST_METHOD'];

// 1. عرض جميع المستخدمين بمختلف رتبهم
if ($method === 'GET') {
    $result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC");
    $users = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    echo json_encode(["success" => true, "users" => $users]);
    exit;
}

// 2. إضافة مستخدم جديد وتحديد دوره
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $password = trim($input["password"] ?? "123456");
    $role = trim($input["role"] ?? "student");

    if ($name === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "الاسم والبريد مطلوبان"]);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم إنشاء المستخدم بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "البريد مسجل مسبقاً"]);
    }
    $stmt->close();
    exit;
}

// 3. تعديل مستخدم ودوره
if ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = intval($input["id"] ?? 0);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $password = trim($input["password"] ?? "");
    $role = trim($input["role"] ?? "student");

    if ($id <= 0 || $name === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "البيانات غير مكتملة"]);
        exit;
    }

    if ($password !== "") {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $hashed, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث المستخدم بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل التحديث"]);
    }
    $stmt->close();
    exit;
}

// 4. حذف مستخدم (مع منع حذف المسؤول لحسابه النشط)
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if ($id === $_SESSION["user_id"]) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "لا يمكنك حذف حسابك الذي تسجل به حالياً"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "تم حذف الحساب"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "المستخدم غير موجود"]);
    }
    $stmt->close();
    exit;
}

$conn->close();
?>
