<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";
require_once "../config.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo "رقم الطالب غير صحيح";
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, user_id, student_number, college, department, level
     FROM students
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "الطالب غير موجود";
    exit;
}

$student = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-edit-page">

<header>

    <h1>تعديل بيانات الطالب</h1>

</header>

<main>

    <h2>تعديل بيانات الطالب</h2>

    <form class="admin-edit-form" action="update_student.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $student["id"]; ?>"
        >

        <label>الرقم الجامعي:</label>
        <br>

        <input
            type="text"
            name="student_number"
            value="<?php echo htmlspecialchars($student["student_number"]); ?>"
            required
        >

        <br><br>

        <label>الكلية:</label>
        <br>

        <input
            type="text"
            name="college"
            value="<?php echo htmlspecialchars($student["college"]); ?>"
            required
        >

        <br><br>

        <label>القسم:</label>
        <br>

        <input
            type="text"
            name="department"
            value="<?php echo htmlspecialchars($student["department"]); ?>"
            required
        >

        <br><br>

        <label>المستوى:</label>
        <br>

        <input
            type="text"
            name="level"
            value="<?php echo htmlspecialchars($student["level"]); ?>"
            required
        >

        <br><br>

        <button type="submit">
            حفظ التعديلات
        </button>

        <a href="students.php">
            إلغاء
        </a>

    </form>

</main>
<script src="../script.js"></script>

</body>

</html>