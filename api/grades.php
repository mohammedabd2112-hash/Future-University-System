<?php
// إعداد ترويسات CORS لتتوافق مع credentials: 'include'
$origin = $_SERVER['HTTP_ORIGIN'] ?? 'http://localhost:5173';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// الرد الفوري على طلب Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config.php';

function calculateGradeLetter($total) {
    if ($total >= 90) return 'ممتاز';
    if ($total >= 80) return 'جيد جداً';
    if ($total >= 70) return 'جيد';
    if ($total >= 60) return 'مقبول';
    return 'راسب';
}

$conn->set_charset("utf8mb4");

// استقبال البيانات سواء أكانت JSON أو POST عادي
$raw_input = file_get_contents("php://input");
$data = json_decode($raw_input, true) ?? [];
$action = $_GET['action'] ?? $data['action'] ?? '';

// --- 1. جلب الدرجات الحقيقية من قاعدة البيانات ---
if ($action === 'get_grades' || $action === 'get' || ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($action))) {
    $grades = [];
    
    // فحص ما إذا كان جدول grades يحتوي على enrollment_id أو student_id
$checkCol = $conn->query("SHOW COLUMNS FROM `grades` LIKE 'enrollment_id'");
$hasEnrollmentId = ($checkCol && $checkCol->num_rows > 0);

if ($hasEnrollmentId) {
    $sql = "SELECT 
        sc.id AS enrollment_id,
        sc.id,
        sc.student_id,
        sc.course_id,
        CONCAT('طالب #', sc.student_id) AS student_name,
        COALESCE(c.course_name, 'مقرر دراسي') AS course_name,
        COALESCE(c.course_code, 'CS101') AS course_code,
        COALESCE(g.midterm_score, 0) AS midterm_score,
        COALESCE(g.practical_score, 0) AS practical_score,
        COALESCE(g.final_score, 0) AS final_score,
        COALESCE(g.total_score, 0) AS total_score,
        '-' AS grade_letter
    FROM student_courses sc
    LEFT JOIN courses c ON sc.course_id = c.id
    LEFT JOIN grades g ON g.enrollment_id = sc.id
    ORDER BY sc.id DESC";
} else {
    // إذا لم يكن هناك ربط مباشر، جلب التسجيلات مع الدرجات إن وجدت
    $sql = "SELECT 
        sc.id AS enrollment_id,
        sc.id,
        sc.student_id,
        sc.course_id,
        CONCAT('طالب #', sc.student_id) AS student_name,
        COALESCE(c.course_name, 'مقرر دراسي') AS course_name,
        COALESCE(c.course_code, 'CS101') AS course_code,
        COALESCE(g.midterm_score, 0) AS midterm_score,
        COALESCE(g.practical_score, 0) AS practical_score,
        COALESCE(g.final_score, 0) AS final_score,
        COALESCE(g.total_score, 0) AS total_score,
        '-' AS grade_letter
    FROM student_courses sc
    LEFT JOIN courses c ON sc.course_id = c.id
    LEFT JOIN grades g ON (g.enrollment_id = sc.id OR g.id = sc.id)
    ORDER BY sc.id DESC";
}

    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $mid = floatval($row['midterm_score']);
            $prac = floatval($row['practical_score']);
            $final = floatval($row['final_score']);
            $total = $mid + $prac + $final;

            $row['midterm_score'] = $mid;
            $row['practical_score'] = $prac;
            $row['final_score'] = $final;
            $row['total_score'] = $total;
            $row['grade_letter'] = ($total > 0) ? calculateGradeLetter($total) : '-';
            $grades[] = $row;
        }
    }

    echo json_encode(['success' => true, 'grades' => $grades]);
    exit();
}

// --- 2. حفظ وتعديل الدرجات في قاعدة البيانات ---
if ($action === 'save_grade' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($data['student_id'] ?? $_POST['student_id'] ?? 0);
    $course_id = intval($data['course_id'] ?? $_POST['course_id'] ?? 0);
    $enrollment_id = intval($data['enrollment_id'] ?? $_POST['enrollment_id'] ?? 0);

    $midterm = floatval($data['midterm_score'] ?? $_POST['midterm_score'] ?? 0);
    $practical = floatval($data['practical_score'] ?? $_POST['practical_score'] ?? 0);
    $final = floatval($data['final_score'] ?? $_POST['final_score'] ?? 0);
    $total = $midterm + $practical + $final;

    // استخراج الطالب والمقرر من جدول التسجيل إذا لم يتوفرا
    if (($student_id <= 0 || $course_id <= 0) && $enrollment_id > 0) {
        $find = $conn->query("SELECT student_id, course_id FROM student_courses WHERE id = $enrollment_id LIMIT 1");
        if ($find && $row = $find->fetch_assoc()) {
            $student_id = intval($row['student_id']);
            $course_id = intval($row['course_id']);
        }
    }

    // التأكد من وجود سجل التسجيل في student_courses
    if ($enrollment_id <= 0 && $student_id > 0 && $course_id > 0) {
        $checkEnr = $conn->query("SELECT id FROM student_courses WHERE student_id = $student_id AND course_id = $course_id LIMIT 1");
        if ($checkEnr && $enrRow = $checkEnr->fetch_assoc()) {
            $enrollment_id = intval($enrRow['id']);
        } else {
            $conn->query("INSERT INTO student_courses (student_id, course_id) VALUES ($student_id, $course_id)");
            $enrollment_id = $conn->insert_id;
        }
    }

    // فحص أعمدة جدول grades المتاحة فعلياً
    $colRes = $conn->query("SHOW COLUMNS FROM `grades`");
    $dbCols = [];
    while ($c = $colRes->fetch_assoc()) {
        $dbCols[] = $c['Field'];
    }

    $saved = false;

    // 1) الحفظ عبر enrollment_id إذا كان العمود موجوداً
    if (in_array('enrollment_id', $dbCols) && $enrollment_id > 0) {
        $chk = $conn->query("SELECT id FROM grades WHERE enrollment_id = $enrollment_id LIMIT 1");
        if ($chk && $chk->num_rows > 0) {
            $saved = $conn->query("UPDATE grades SET midterm_score = $midterm, practical_score = $practical, final_score = $final, total_score = $total WHERE enrollment_id = $enrollment_id");
        } else {
            $saved = $conn->query("INSERT INTO grades (enrollment_id, midterm_score, practical_score, final_score, total_score) VALUES ($enrollment_id, $midterm, $practical, $final, $total)");
        }
    } 
    // 2) أو الحفظ عبر student_id و course_id إذا كانا موجودين
    else if (in_array('student_id', $dbCols) && in_array('course_id', $dbCols)) {
        $chk = $conn->query("SELECT id FROM grades WHERE student_id = $student_id AND course_id = $course_id LIMIT 1");
        if ($chk && $chk->num_rows > 0) {
            $saved = $conn->query("UPDATE grades SET midterm_score = $midterm, practical_score = $practical, final_score = $final, total_score = $total WHERE student_id = $student_id AND course_id = $course_id");
        } else {
            $saved = $conn->query("INSERT INTO grades (student_id, course_id, midterm_score, practical_score, final_score, total_score) VALUES ($student_id, $course_id, $midterm, $practical, $final, $total)");
        }
    } 
    // 3) أو الحفظ برقم السجل المباشر id
    else if ($enrollment_id > 0) {
        $chk = $conn->query("SELECT id FROM grades WHERE id = $enrollment_id LIMIT 1");
        if ($chk && $chk->num_rows > 0) {
            $saved = $conn->query("UPDATE grades SET midterm_score = $midterm, practical_score = $practical, final_score = $final, total_score = $total WHERE id = $enrollment_id");
        } else {
            $saved = $conn->query("INSERT INTO grades (id, midterm_score, practical_score, final_score, total_score) VALUES ($enrollment_id, $midterm, $practical, $final, $total)");
        }
    }

    if ($saved) {
        echo json_encode(['success' => true, 'message' => 'تم حفظ الدرجة بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل الحفظ: ' . $conn->error]);
    }
    exit();
}
