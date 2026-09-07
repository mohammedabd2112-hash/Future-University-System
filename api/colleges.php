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

// التأكد من وجود جدول الكليات
$conn->query("CREATE TABLE IF NOT EXISTS colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

// 1. جلب قائمة الكليات
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM colleges ORDER BY id DESC");
    $colleges = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $colleges[] = $row;
        }
    }
    echo json_encode(["success" => true, "colleges" => $colleges]);
    exit;
}

// 2. إضافة كلية جديدة
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $name = trim($input["name"] ?? "");
    $description = trim($input["description"] ?? "");

    if ($name === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "اسم الكلية مطلوب"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO colleges (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تمت إضافة الكلية بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل حفظ الكلية"]);
    }
    $stmt->close();
    exit;
}

// 3. تعديل بيانات كلية
if ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = intval($input["id"] ?? 0);
    $name = trim($input["name"] ?? "");
    $description = trim($input["description"] ?? "");

    if ($id <= 0 || $name === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "بيانات الكلية غير مكتملة"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE colleges SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث الكلية بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل تحديث البيانات"]);
    }
    $stmt->close();
    exit;
}

// 4. حذف كلية
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM colleges WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "تم حذف الكلية"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "تعذر العثور على الكلية"]);
    }
    $stmt->close();
    exit;
}

$conn->close();
?>
