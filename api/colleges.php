<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config.php';

$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true) ?? [];
$action = $_GET['action'] ?? $data['action'] ?? '';

// 1. الحذف أولاً (لكي لا يختلط بطلب الـ GET العادي)
if ($action === 'delete' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = intval($_GET['id'] ?? $data['id'] ?? $_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرّف الكلية غير صحيح']);
        exit();
    }

    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $stmt = $conn->prepare("DELETE FROM colleges WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        echo json_encode(['success' => true, 'message' => 'تم الحذف بنجاح', 'deleted_id' => $id]);
    } else {
        $err = $stmt->error;
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        echo json_encode(['success' => false, 'message' => 'خطأ أثناء الحذف: ' . $err]);
    }
    exit();
}

// 2. تعديل كلية
if ($action === 'edit') {
    $id = intval($data['id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');

    $stmt = $conn->prepare("UPDATE colleges SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم تعديل الكلية بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل التعديل: ' . $conn->error]);
    }
    exit();
}

// 3. إضافة كلية
if ($action === 'add' || ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($action))) {
    $name = trim($data['name'] ?? $_POST['name'] ?? '');
    $description = trim($data['description'] ?? $_POST['description'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'اسم الكلية مطلوب']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO colleges (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تمت إضافة الكلية بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل حفظ الكلية: ' . $conn->error]);
    }
    exit();
}

// 4. جلب قائمة الكليات (الافتراضي)
$result = $conn->query("SELECT * FROM colleges ORDER BY id DESC");
$colleges = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $colleges[] = $row;
    }
}
echo json_encode(['success' => true, 'colleges' => $colleges]);
exit();
