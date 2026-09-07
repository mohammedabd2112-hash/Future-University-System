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

// 1. عدد الطلاب
$students_count = 0;
$res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'student'");
if ($res && $row = $res->fetch_assoc()) $students_count = intval($row['c']);

// 2. عدد المدرسين
$teachers_count = 0;
if (tableExists($conn, 'teachers')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM teachers");
    if ($res && $row = $res->fetch_assoc()) $teachers_count = intval($row['c']);
} else {
    $res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'teacher'");
    if ($res && $row = $res->fetch_assoc()) $teachers_count = intval($row['c']);
}

// 3. عدد الكليات
$colleges_count = 0;
if (tableExists($conn, 'colleges')) {
    $res = $conn->query("SELECT COUNT(*) AS c FROM colleges");
    if ($res && $row = $res->fetch_assoc()) $colleges_count = intval($row['c']);
}

// 4. الأخبار وآخر الإعلانات
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

// 5. آخر الطلاب المسجلين حديثاً
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
        "news" => $news_count
    ],
    "recent_students" => $recent_students,
    "recent_news" => $recent_news
]);

$conn->close();
?>
