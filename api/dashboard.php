<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = ['http://localhost:5173', 'http://localhost:5174', 'http://localhost:5175'];

if (in_array($origin, $allowed)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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

function tableExists($conn, $table) {
    $res = $conn->query("SHOW TABLES LIKE '$table'");
    return $res && $res->num_rows > 0;
}

// 1. إجمالي الطلاب
$students_count = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'student'");
if ($res && $row = $res->fetch_assoc()) $students_count = intval($row['c']);

// 2. إجمالي المدرسين
$teachers_count = 0;
if (tableExists($conn, 'teachers')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM teachers");
    if ($res && $row = $res->fetch_assoc()) $teachers_count = intval($row['c']);
} else {
    $res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'teacher'");
    if ($res && $row = $res->fetch_assoc()) $teachers_count = intval($row['c']);
}

// 3. إجمالي الكليات
$colleges_count = 0;
if (tableExists($conn, 'colleges')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM colleges");
    if ($res && $row = $res->fetch_assoc()) $colleges_count = intval($row['c']);
}

// 4. الأخبار والإعلانات
$news_count = 0;
$recent_news = [];
if (tableExists($conn, 'news')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM news");
    if ($res && $row = $res->fetch_assoc()) $news_count = intval($row['c']);

    $res_n = $conn->query("SELECT id, title, created_at FROM news ORDER BY id DESC LIMIT 4");
    if ($res_n) {
        while ($row = $res_n->fetch_assoc()) {
            $recent_news[] = $row;
        }
    }
}

// 5. إجمالي المستخدمين
$users_count = 0;
$res_u = $conn->query("SELECT COUNT(*) AS c FROM users");
if ($res_u && $row = $res_u->fetch_assoc()) $users_count = intval($row['c']);

// 6. المقررات الدراسية
$courses_count = 0;
if (tableExists($conn, 'courses')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM courses");
    if ($res && $row = $res->fetch_assoc()) $courses_count = intval($row['c']);
}

// 7. سجلات الكنترول والدرجات
$grades_count = 0;
if (tableExists($conn, 'grades')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM grades");
    if ($res && $row = $res->fetch_assoc()) $grades_count = intval($row['c']);
}

// 8. عقود ومسيرات الموارد البشرية
$payroll_count = 0;
if (tableExists($conn, 'hr_contracts')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM hr_contracts");
    if ($res && $row = $res->fetch_assoc()) $payroll_count = intval($row['c']);
}

// 9. الأصول والمخازن
$assets_count = 0;
if (tableExists($conn, 'inventory_assets')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM inventory_assets");
    if ($res && $row = $res->fetch_assoc()) $assets_count = intval($row['c']);
}

// 10. إجمالي مبالغ الفواتير / المالية
$finance_total = 0;
if (tableExists($conn, 'invoices')) {
    $res = $conn->query("SELECT COALESCE(SUM(amount), 0) AS s FROM invoices");
    if ($res && $row = $res->fetch_assoc()) $finance_total = floatval($row['s']);
}

// آخر الطلاب المسجلين حديثاً
$recent_students = [];
$res_s = $conn->query("SELECT id, name, email FROM users WHERE role = 'student' ORDER BY id DESC LIMIT 5");
if ($res_s) {
    while ($row = $res_s->fetch_assoc()) {
        $recent_students[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "stats" => [
        "students" => $students_count,
        "teachers" => $teachers_count,
        "colleges" => $colleges_count,
        "news"     => $news_count,
        "users"    => $users_count,
        "courses"  => $courses_count,
        "grades"   => $grades_count,
        "payroll"  => $payroll_count,
        "assets"   => $assets_count,
        "finance"  => $finance_total
    ],
    "recent_students" => $recent_students,
    "recent_news"     => $recent_news
]);

$conn->close();
?>