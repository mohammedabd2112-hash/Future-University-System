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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الطلاب</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <header class="site-header">
        <div class="site-header__inner">
            <a class="brand" href="../index.php" aria-label="جامعة المستقبل - الرئيسية">
                <span class="brand__mark" aria-hidden="true">FU</span>
                <span><strong>Future University</strong><small>البوابة الجامعية</small></span>
            </a>
            <nav class="site-nav" aria-label="التنقل الرئيسي">
                <a href="../index.php">الرئيسية</a>
                <a href="colleges.php">الكليات</a>
                <a class="is-active" href="students.php" aria-current="page">الطلاب</a>
                <a href="teachers.php">أعضاء هيئة التدريس</a>
                <a href="news.php">الأخبار</a>
                <a class="nav-login" href="../api/logout.php">تسجيل الخروج</a>
            </nav>
        </div>
    </header>

    <main class="public-main">
        <header class="page-header">
            <p class="breadcrumb"><a href="../index.php">الرئيسية</a><span>/</span>بوابة الطلاب</p>
            <p class="section-kicker">الخدمات الأكاديمية</p>
            <h1>بوابة الطلاب</h1>
            <p>مرحبًا، <?php echo htmlspecialchars($_SESSION["user_name"]); ?>. هذه بياناتك الأكاديمية المسجلة.</p>
        </header>

        <section class="student-profile" aria-labelledby="student-data-title">
            <div class="section-heading">
                <div><p class="section-kicker">الملف الأكاديمي</p><h2 id="student-data-title">بيانات الطالب</h2></div>
            </div>
            <dl class="details-grid">
                <div><dt>الرقم الجامعي</dt><dd><?php echo htmlspecialchars($student["student_number"]); ?></dd></div>
                <div><dt>الكلية</dt><dd><?php echo htmlspecialchars($student["college"]); ?></dd></div>
                <div><dt>القسم</dt><dd><?php echo htmlspecialchars($student["department"]); ?></dd></div>
                <div><dt>المستوى</dt><dd><?php echo htmlspecialchars($student["level"]); ?></dd></div>
            </dl>
        </section>
    </main>

    <footer class="site-footer"><div class="site-footer__inner"><strong>Future University</strong><span>البوابة الجامعية الرسمية</span></div></footer>
    <script src="../script.js"></script>

</body>

</html>