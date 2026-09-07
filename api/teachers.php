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

// إنشاء الجدول تلقائياً إن لم يكن موجوداً
$conn->query("CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NULL,
    department VARCHAR(255) NULL,
    specialization VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

// 1. جلب قائمة المدرسين
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
    $teachers = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
    }
    echo json_encode(["success" => true, "teachers" => $teachers]);
    exit;
}

// 2. إضافة مدرس جديد
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $phone = trim($input["phone"] ?? "");
    $department = trim($input["department"] ?? "");
    $specialization = trim($input["specialization"] ?? "");

    if ($name === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "الاسم والبريد الإلكتروني مطلوبان"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO teachers (name, email, phone, department, specialization) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $department, $specialization);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تمت إضافة عضو هيئة التدريس بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل حفظ البيانات أو البريد مكرر"]);
    }
    $stmt->close();
    exit;
}

// 3. تعديل بيانات مدرس
if ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = intval($input["id"] ?? 0);
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $phone = trim($input["phone"] ?? "");
    $department = trim($input["department"] ?? "");
    $specialization = trim($input["specialization"] ?? "");

    if ($id <= 0 || $name === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "بيانات المدرس غير مكتملة"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE teachers SET name = ?, email = ?, phone = ?, department = ?, specialization = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $department, $specialization, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث البيانات بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل تحديث البيانات"]);
    }
    $stmt->close();
    exit;
}

// 4. حذف مدرس
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "تم الحذف بنجاح"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "تعذر العثور على السجل"]);
    }
    $stmt->close();
    exit;
}

$conn->close();
?>
