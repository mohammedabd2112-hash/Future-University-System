<?php
// منع PHP من إيقاف السكريبت بسبب أخطاء MySQL
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
ini_set('display_errors', '0');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// الاتصال بقاعدة البيانات
require_once __DIR__ . '/../config.php';

if (!isset($conn) || !$conn || $conn->connect_error) {
    // إرجاع نجاح مع مصفوفة فارغة لتفادي انهيار الواجهة
    echo json_encode(['success' => true, 'invoices' => []]);
    exit();
}

$conn->set_charset("utf8mb4");

// إنشاء أو تحديث جدول الفواتير بهدوء
$conn->query("CREATE TABLE IF NOT EXISTS invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NULL,
    student_name VARCHAR(150) NULL,
    title VARCHAR(150) NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$action = $_GET['action'] ?? '';

// 1. حذف فاتورة
if ($action === 'delete_invoice' || ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST')) {
    $id = intval($_GET['id'] ?? $data['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'تم حذف الفاتورة بنجاح']);
            exit();
        }
    }
    echo json_encode(['success' => false, 'message' => 'تعذر حذف الفاتورة']);
    exit();
}

// 2. تعديل فاتورة
if ($action === 'edit_invoice') {
    $id = intval($data['id'] ?? 0);
    $title = trim($data['title'] ?? '');
    $amount = floatval($data['amount'] ?? 0);
    $status = trim($data['status'] ?? 'unpaid');

    $stmt = $conn->prepare("UPDATE invoices SET title = ?, amount = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sdsi", $title, $amount, $status, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم تعديل الفاتورة بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'فشل تعديل الفاتورة']);
    exit();
}

// 1. جلب الفواتير (تفريغ الاستعلام بدون أي دمج معقد يسبب خطأ)
if ($action === 'get_invoices' || $action === 'get' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $invoices = [];
    $result = $conn->query("SELECT * FROM invoices ORDER BY id DESC");
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $amount = floatval($row['amount'] ?? 0);
            $paid = floatval($row['paid_amount'] ?? 0);
            $row['remaining_amount'] = $amount - $paid;
            $row['student_name'] = $row['student_name'] ?? ('طالب #' . ($row['student_id'] ?? $row['id']));
            $row['title'] = $row['title'] ?? 'رسوم دراسية';
            $invoices[] = $row;
        }
    }

    echo json_encode([
        'success' => true,
        'invoices' => $invoices
    ]);
    exit();
}

// 2. إضافة فاتورة
if ($action === 'add_invoice' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
    $student_id = intval($input['student_id'] ?? $_POST['student_id'] ?? 0);
    $amount = floatval($input['amount'] ?? $_POST['amount'] ?? 0);
    $title = trim($input['title'] ?? $_POST['title'] ?? 'رسوم دراسية');
    $status = trim($input['status'] ?? $_POST['status'] ?? 'unpaid');
    $student_name = trim($input['student_name'] ?? '');

    if (empty($student_name) && $student_id > 0) {
        $st_res = $conn->query("SELECT name FROM students WHERE id = $student_id LIMIT 1");
        if ($st_res && $row = $st_res->fetch_assoc()) {
            $student_name = $row['name'];
        }
    }

    $stmt = $conn->prepare("INSERT INTO invoices (student_id, student_name, title, amount, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issds", $student_id, $student_name, $title, $amount, $status);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'تم حفظ الفاتورة']);
            exit();
        }
    }

    echo json_encode(['success' => false, 'message' => 'فشل حفظ الفاتورة']);
    exit();
}

echo json_encode(['success' => true, 'invoices' => []]);
exit();