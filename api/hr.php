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

$conn->query("CREATE TABLE IF NOT EXISTS hr_contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL UNIQUE,
    contract_type VARCHAR(50) DEFAULT 'دوام كامل',
    base_salary DECIMAL(10,2) DEFAULT 0,
    teaching_hours INT DEFAULT 0,
    hourly_rate DECIMAL(10,2) DEFAULT 25,
    net_salary DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'معتمد'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true) ?? [];
$action = $_GET['action'] ?? $data['action'] ?? '';

// --- عملية الحذف الفعلية ---
if ($action === 'delete') {
    $t_id = intval($_GET['teacher_id'] ?? $_GET['id'] ?? $data['teacher_id'] ?? 0);
    if ($t_id > 0) {
        $conn->query("DELETE FROM hr_contracts WHERE teacher_id = $t_id");
        $conn->query("DELETE FROM teachers WHERE id = $t_id");
        echo json_encode(['success' => true, 'message' => 'تم الحذف بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'معرف غير صالح']);
    exit();
}

// --- عملية الحفظ / التعديل ---
if ($action === 'save' || $action === 'edit' || !empty($data['teacher_id'])) {
    $t_id = intval($data['teacher_id'] ?? 0);
    $type = trim($data['contract_type'] ?? 'دوام كامل');
    $base = floatval($data['base_salary'] ?? 0);
    $hours = intval($data['teaching_hours'] ?? 0);
    $rate = floatval($data['hourly_rate'] ?? 25);
    $net = $base + ($hours * $rate);

    if ($t_id > 0) {
        $conn->query("INSERT INTO hr_contracts (teacher_id, contract_type, base_salary, teaching_hours, hourly_rate, net_salary) 
                      VALUES ($t_id, '$type', $base, $hours, $rate, $net)
                      ON DUPLICATE KEY UPDATE 
                        contract_type = '$type', 
                        base_salary = $base, 
                        teaching_hours = $hours, 
                        hourly_rate = $rate, 
                        net_salary = $net");
        echo json_encode(['success' => true, 'message' => 'تم الحفظ والتعديل بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'يرجى تحديد العضو']);
    exit();
}

// --- جلب البيانات ---
$payroll = [];
$res = $conn->query("SELECT 
        t.id AS teacher_id,
        t.name AS teacher_name,
        COALESCE(t.department, 'عام') AS department,
        COALESCE(c.contract_type, 'دوام كامل') AS contract_type,
        COALESCE(c.base_salary, 1200) AS base_salary,
        COALESCE(c.teaching_hours, 18) AS teaching_hours,
        COALESCE(c.hourly_rate, 25) AS hourly_rate,
        COALESCE(c.net_salary, 1650) AS net_salary,
        COALESCE(c.status, 'معتمد') AS status
    FROM teachers t
    LEFT JOIN hr_contracts c ON t.id = c.teacher_id
    ORDER BY t.id ASC");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $payroll[] = [
            'teacher_id' => intval($row['teacher_id']),
            'teacher_name' => $row['teacher_name'],
            'department' => $row['department'],
            'contract_type' => $row['contract_type'],
            'base_salary' => floatval($row['base_salary']),
            'teaching_hours' => intval($row['teaching_hours']),
            'hourly_rate' => floatval($row['hourly_rate']),
            'net_salary' => floatval($row['net_salary']),
            'status' => $row['status']
        ];
    }
}

echo json_encode(['success' => true, 'payroll' => $payroll]);
exit();
