<?php
// إيقاف إظهار أخطاء HTML لضمان خروج JSON نظيف
error_reporting(0);
ini_set('display_errors', 0);

// ترويسات الـ CORS الكاملة لمنع أخطاء المتصفح
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config.php';

// إنشاء جدول المقررات تلقائياً إن لم يكن موجوداً
$table_sql = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    credits INT DEFAULT 3,
    college_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($table_sql);

$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true) ?? [];
$action = $_GET['action'] ?? $data['action'] ?? '';

// 1. حذف مقرر
if ($action === 'delete' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = intval($_GET['id'] ?? $data['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'تم حذف المقرر بنجاح']);
            exit();
        }
    }
    echo json_encode(['success' => false, 'message' => 'تعذر حذف المقرر']);
    exit();
}

// 2. إضافة مقرر جديد
if ($action === 'add' || ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($data['id']))) {
    $name = trim($data['name'] ?? $_POST['name'] ?? '');
    $code = trim($data['code'] ?? $_POST['code'] ?? '');
    $credits = intval($data['credits'] ?? $_POST['credits'] ?? 3);
    $college_id = intval($data['college_id'] ?? $_POST['college_id'] ?? 0);

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'اسم المقرر مطلوب']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO courses (name, code, credits, college_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $name, $code, $credits, $college_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تمت إضافة المقرر بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل الحفظ: ' . $conn->error]);
    }
    exit();
}

// 3. تعديل مقرر
if ($action === 'edit' || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['id']))) {
    $id = intval($data['id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $code = trim($data['code'] ?? '');
    $credits = intval($data['credits'] ?? 3);

    $stmt = $conn->prepare("UPDATE courses SET name = ?, code = ?, credits = ? WHERE id = ?");
    $stmt->bind_param("ssii", $name, $code, $credits, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم تعديل المقرر بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل التعديل: ' . $conn->error]);
    }
    exit();
}

// 4. جلب المقررات (الافتراضي: يدعم action=get_courses أو action=get أو طلب GET عادي)
$result = $conn->query("SELECT * FROM courses ORDER BY id DESC");
$courses = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

echo json_encode(['success' => true, 'courses' => $courses]);
exit();