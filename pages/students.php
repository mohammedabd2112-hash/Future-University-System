<?php

require_once "../includes/student_auth.php";
require_once "../config.php";

if ($_SESSION["user_role"] !== "student") {
    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    exit;
}

require_once "../config.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT student_number, college, department, level
    FROM students
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "لم يتم العثور على بيانات الطالب";
    exit;
}

$student = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>الطلاب</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <header>

        <h1>جامعة المستقبل</h1>

        <nav>
            <a href="../index.php">الرئيسية</a>
            <a href="colleges.php">الكليات</a>
            <a href="students.php">الطلاب</a>
            <a href="news.php">الأخبار</a>
            <a href="login.html">تسجيل الخروج</a>
        </nav>

    </header>

    <main>

        <h2>بوابة الطلاب</h2>

        <p>
            مرحبًا، <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </p>

        <h3>بيانات الطالب</h3>

        <p>
            <strong>الرقم الجامعي:</strong>
            <?php echo htmlspecialchars($student["student_number"]); ?>
        </p>

        <p>
            <strong>الكلية:</strong>
            <?php echo htmlspecialchars($student["college"]); ?>
        </p>

        <p>
            <strong>القسم:</strong>
            <?php echo htmlspecialchars($student["department"]); ?>
        </p>

        <p>
            <strong>المستوى:</strong>
            <?php echo htmlspecialchars($student["level"]); ?>
        </p>

    </main>

</body>

</html>