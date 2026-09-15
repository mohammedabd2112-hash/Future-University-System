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

$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true) ?? [];
$action = $_GET['action'] ?? $data['action'] ?? '';

// 1. حذف مقرر (أولوية أولى)
if ($action === 'delete_course' || $action === 'delete') {
    $course_id = intval($_GET['id'] ?? $data['id'] ?? $_POST['id'] ?? 0);
    if ($course_id > 0) {
        $conn->query("DELETE FROM student_courses WHERE course_id = $course_id");
        $conn->query("DELETE FROM grades WHERE course_id = $course_id");
        $conn->query("DELETE FROM courses WHERE id = $course_id");
        echo json_encode(['success' => true, 'message' => 'تم حذف المقرر بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'معرف المقرر غير صالح']);
    exit();
}

// 2. تعديل مقرر (أولوية ثانية قبل الإضافة)
if ($action === 'edit_course' || $action === 'update') {
    $course_id = intval($data['id'] ?? $_POST['id'] ?? $_GET['id'] ?? 0);
    $name = trim($data['name'] ?? $_POST['name'] ?? $data['course_name'] ?? '');
    $code = trim($data['code'] ?? $_POST['code'] ?? $data['course_code'] ?? '');
    $credits = intval($data['credits'] ?? $_POST['credits'] ?? $data['credit_hours'] ?? 3);
    $college_id = intval($data['college_id'] ?? $_POST['college_id'] ?? 1);

    if ($course_id > 0 && !empty($name) && !empty($code)) {
        // تحديث الأعمدة المتوفرة
        $conn->query("UPDATE courses SET name='$name', code='$code', credits=$credits, college_id=$college_id WHERE id=$course_id");
        $conn->query("UPDATE courses SET course_name='$name', course_code='$code' WHERE id=$course_id");
        echo json_encode(['success' => true, 'message' => 'تم تعديل المقرر بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'بيانات التعديل غير مكتملة']);
    exit();
}

// 3. تسجيل طالب في مقرر
if ($action === 'enroll' || $action === 'enroll_student') {
    $student_id = intval($data['student_id'] ?? $_POST['student_id'] ?? 0);
    $course_id = intval($data['course_id'] ?? $_POST['course_id'] ?? 0);

    if ($student_id > 0 && $course_id > 0) {
        $conn->query("INSERT IGNORE INTO student_courses (student_id, course_id) VALUES ($student_id, $course_id)");
        $conn->query("INSERT IGNORE INTO grades (student_id, course_id, midterm_score, practical_score, final_score, total_score, grade_letter) VALUES ($student_id, $course_id, 0, 0, 0, 0, '-')");
        echo json_encode(['success' => true, 'message' => 'تم تسجيل الطالب بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
    exit();
}

// 4. إضافة مقرر جديد
if ($action === 'add_course' || $action === 'add' || $action === 'create') {
    $name = trim($data['name'] ?? $_POST['name'] ?? $data['course_name'] ?? '');
    $code = trim($data['code'] ?? $_POST['code'] ?? $data['course_code'] ?? '');
    $credits = intval($data['credits'] ?? $_POST['credits'] ?? 3);
    $college_id = intval($data['college_id'] ?? $_POST['college_id'] ?? 1);

    if (!empty($name) && !empty($code)) {
        $conn->query("INSERT INTO courses (name, code, credits, college_id) VALUES ('$name', '$code', $credits, $college_id)");
        $conn->query("INSERT INTO courses (course_name, course_code, credits, college_id) VALUES ('$name', '$code', $credits, $college_id)");
        echo json_encode(['success' => true, 'message' => 'تم إضافة المقرر بنجاح']);
        exit();
    }
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال اسم ورمز المقرر']);
    exit();
}

// 5. جلب المقررات
$courses = [];
$res = $conn->query("SELECT * FROM courses ORDER BY id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $c_name = $row['name'] ?? $row['course_name'] ?? '';
        $c_code = $row['code'] ?? $row['course_code'] ?? '';
        $c_credits = intval($row['credits'] ?? $row['credit_hours'] ?? 3);
        $c_col = intval($row['college_id'] ?? 1);

        $courses[] = [
            'id' => intval($row['id']),
            'name' => $c_name,
            'course_name' => $c_name,
            'code' => $c_code,
            'course_code' => $c_code,
            'credits' => $c_credits,
            'credit_hours' => $c_credits,
            'college_id' => $c_col
        ];
    }
}

echo json_encode(['success' => true, 'courses' => $courses]);
exit();
