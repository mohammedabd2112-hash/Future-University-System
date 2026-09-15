<?php
mysqli_report(MYSQLI_REPORT_OFF);
error_reporting(0);
ini_set('display_errors', '0');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config.php';
$conn->set_charset("utf8mb4");

// إنشاء جدول الأصول والمخازن تلقائياً
$conn->query("CREATE TABLE IF NOT EXISTS inventory_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    location VARCHAR(150) NOT NULL,
    quantity INT DEFAULT 1,
    status VARCHAR(50) DEFAULT 'متاح',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true) ?? [];
$action = $_GET['action'] ?? $data['action'] ?? '';

// 1. حذف أصل أو عهدة
if ($action === 'delete') {
    $id = intval($_GET['id'] ?? $data['id'] ?? 0);
    if ($id > 0) {
        $conn->query("DELETE FROM inventory_assets WHERE id = $id");
        echo json_encode(['success' => true, 'message' => 'تم حذف الأصل بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'معرف غير صالح']);
    exit();
}

// 2. إضافة أصل جديد
if ($action === 'add') {
    $name = trim($data['asset_name'] ?? '');
    $cat = trim($data['category'] ?? 'أجهزة ومعدات');
    $loc = trim($data['location'] ?? 'المبنى الرئيسي');
    $qty = intval($data['quantity'] ?? 1);
    $status = trim($data['status'] ?? 'متاح');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'يرجى إدخال اسم الأصل']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO inventory_assets (asset_name, category, location, quantity, status) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssis", $name, $cat, $loc, $qty, $status);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'تمت إضافة الأصل بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'فشل الحفظ']);
    exit();
}

// 3. تعديل أصل
if ($action === 'edit') {
    $id = intval($data['id'] ?? 0);
    $name = trim($data['asset_name'] ?? '');
    $cat = trim($data['category'] ?? 'أجهزة ومعدات');
    $loc = trim($data['location'] ?? 'المبنى الرئيسي');
    $qty = intval($data['quantity'] ?? 1);
    $status = trim($data['status'] ?? 'متاح');

    if ($id > 0 && !empty($name)) {
        $stmt = $conn->prepare("UPDATE inventory_assets SET asset_name=?, category=?, location=?, quantity=?, status=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("sssisi", $name, $cat, $loc, $qty, $status, $id);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'تم تعديل الأصل بنجاح']);
            exit();
        }
    }
    echo json_encode(['success' => false, 'message' => 'بيانات غير مكتملة']);
    exit();
}

// 4. جلب جميع الأصول والمخزون
$assets = [];
$res = $conn->query("SELECT * FROM inventory_assets ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $assets[] = [
            'id' => intval($row['id']),
            'asset_name' => $row['asset_name'],
            'category' => $row['category'],
            'location' => $row['location'],
            'quantity' => intval($row['quantity']),
            'status' => $row['status']
        ];
    }
}

echo json_encode(['success' => true, 'assets' => $assets]);
exit();
