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

// إنشاء جدول الأخبار إن لم يكن موجوداً
$conn->query("CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

// 1. جلب قائمة الأخبار
if ($method === 'GET') {
    $result = $conn->query("SELECT id, title, content, DATE_FORMAT(created_at, '%Y-%m-%d') AS date FROM news ORDER BY id DESC");
    $news = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $news[] = $row;
        }
    }
    echo json_encode(["success" => true, "news" => $news]);
    exit;
}

// 2. إضافة خبر جديد
if ($method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $title = trim($input["title"] ?? "");
    $content = trim($input["content"] ?? "");

    if ($title === "" || $content === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "عنوان الخبر ومحتواه مطلوبان"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم نشر الخبر بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل نشر الخبر"]);
    }
    $stmt->close();
    exit;
}

// 3. تعديل خبر
if ($method === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = intval($input["id"] ?? 0);
    $title = trim($input["title"] ?? "");
    $content = trim($input["content"] ?? "");

    if ($id <= 0 || $title === "" || $content === "") {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "بيانات الخبر غير مكتملة"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث الخبر بنجاح"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "فشل تحديث الخبر"]);
    }
    $stmt->close();
    exit;
}

// 4. حذف خبر
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "تم حذف الخبر"]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "الخبر غير موجود"]);
    }
    $stmt->close();
    exit;
}

$conn->close();
?>
