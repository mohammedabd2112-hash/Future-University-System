<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>لوحة الإدارة - جامعة المستقبل</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-brand" href="index.php">
            <span class="brand__mark" aria-hidden="true">FU</span>
            <span><strong>Future University</strong><small>نظام الإدارة</small></span>
        </a>
        <nav class="admin-nav" aria-label="تنقل الإدارة">
            <a class="is-active" href="index.php" aria-current="page"><span aria-hidden="true">01</span>لوحة التحكم</a>
            <a href="students.php"><span aria-hidden="true">02</span>الطلاب</a>
            <a href="teachers.php"><span aria-hidden="true">03</span>أعضاء هيئة التدريس</a>
            <a href="colleges.php"><span aria-hidden="true">04</span>الكليات</a>
            <a href="news.php"><span aria-hidden="true">05</span>الأخبار</a>
            <a href="users.php"><span aria-hidden="true">06</span>المستخدمون</a>
        </nav>
        <a class="admin-logout" href="../api/logout.php">تسجيل الخروج</a>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <div><p class="section-kicker">Administrative system</p><h1>لوحة التحكم</h1></div>
            <div class="admin-user"><span class="admin-user__mark" aria-hidden="true">FU</span><span><?php echo htmlspecialchars($_SESSION["user_name"]); ?></span></div>
        </header>

        <main class="admin-main">
            <section class="admin-welcome">
                <p class="section-kicker">نظرة عامة</p>
                <h2>مرحبًا، <?php echo htmlspecialchars($_SESSION["user_name"]); ?></h2>
                <p>استخدم أقسام النظام لإدارة المعلومات الأكاديمية والمحتوى المنشور.</p>
            </section>

            <section class="admin-section" aria-labelledby="admin-sections-title">
                <div class="section-heading"><div><p class="section-kicker">مساحات العمل</p><h2 id="admin-sections-title">إدارة النظام</h2></div></div>
                <div class="admin-links">
                    <a class="admin-link" href="students.php"><span class="admin-link__number">01</span><span><strong>الطلاب</strong><small>إضافة وتعديل البيانات الأكاديمية</small></span><span aria-hidden="true">←</span></a>
                    <a class="admin-link" href="teachers.php"><span class="admin-link__number">02</span><span><strong>أعضاء هيئة التدريس</strong><small>التخصصات وبيانات التواصل</small></span><span aria-hidden="true">←</span></a>
                    <a class="admin-link" href="colleges.php"><span class="admin-link__number">03</span><span><strong>الكليات</strong><small>إدارة الكليات والأقسام</small></span><span aria-hidden="true">←</span></a>
                    <a class="admin-link" href="news.php"><span class="admin-link__number">04</span><span><strong>الأخبار</strong><small>نشر وتحديث أخبار الجامعة</small></span><span aria-hidden="true">←</span></a>
                    <a class="admin-link" href="users.php"><span class="admin-link__number">05</span><span><strong>المستخدمون</strong><small>الحسابات والصلاحيات</small></span><span aria-hidden="true">←</span></a>
                </div>
            </section>
        </main>
    </div>
    <script src="../script.js"></script>

</body>

</html>