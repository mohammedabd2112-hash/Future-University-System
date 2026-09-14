<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config.php';

$action = $_GET['action'] ?? '';

// 1. جلب سجلات الطلاب المسجلين مع درجاتهم الحالية
if ($action === 'get_enrollments_grades') {
    $sql = "SELECT e.id as enrollment_id, e.academic_year, e.semester,
                   s.id as student_id, s.name as student_name,
                   c.id as course_id, c.course_code, c.course_name, c.credit_hours,
                   COALESCE(g.midterm_score, 0) as midterm_score,
                   COALESCE(g.practical_score, 0) as practical_score,
                   COALESCE(g.final_score, 0) as final_score,
                   COALESCE(g.total_score, 0) as total_score,
                   COALESCE(g.grade_rating, 'لم ترصد') as grade_rating
            FROM enrollments e
            JOIN students s ON e.student_id = s.id
            JOIN courses c ON e.course_id = c.id
            LEFT JOIN grades g ON e.id = g.enrollment_id
            ORDER BY e.id DESC";

    $result = $conn->query($sql);
    $records = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }
    echo json_encode(['success' => true, 'records' => $records]);
    exit();
}

// 2. رصد أو تعديل درجات طالب في مقرر
if ($action === 'save_grade' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $enrollment_id = intval($data['enrollment_id'] ?? 0);
    $midterm = floatval($data['midterm_score'] ?? 0);
    $practical = floatval($data['practical_score'] ?? 0);
    $final = floatval($data['final_score'] ?? 0);

    if ($enrollment_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرّف التسجيل غير صحيح']);
        exit();
    }

    // حساب المجموع وتحديد التقدير العربي تلقائياً
    $total = $midterm + $practical + $final;
    $rating = 'ضعيف';

    if ($total >= 90) {
        $rating = 'ممتاز';
    } elseif ($total >= 80) {
        $rating = 'جيد جداً';
    } elseif ($total >= 70) {
        $rating = 'جيد';
    } elseif ($total >= 50) {
        $rating = 'مقبول';
    } else {
        $rating = 'ضعيف';
    }

    // إدخال الدرجات أو تحديثها إذا كانت مدخلة مسبقاً (UPSERT)
    $stmt = $conn->prepare("
        INSERT INTO grades (enrollment_id, midterm_score, practical_score, final_score, grade_rating)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            midterm_score = VALUES(midterm_score),
            practical_score = VALUES(practical_score),
            final_score = VALUES(final_score),
            grade_rating = VALUES(grade_rating)
    ");
    $stmt->bind_param("iddds", $enrollment_id, $midterm, $practical, $final, $rating);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'تم حفظ ورصد الدرجات بنجاح', 'rating' => $rating]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل حفظ الدرجات: ' . $conn->error]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);