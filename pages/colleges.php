<?php

require_once "../config.php";

$sql = "
    SELECT id, name, description, dean, created_at
    FROM colleges
    ORDER BY created_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كليات الجامعة</title>

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
                <a class="is-active" href="colleges.php" aria-current="page">الكليات</a>
                <a href="login.html">الطلاب</a>
                <a href="teachers.php">أعضاء هيئة التدريس</a>
                <a href="news.php">الأخبار</a>
                <a class="nav-login" href="login.html">تسجيل الدخول</a>
            </nav>
        </div>
    </header>

    <main class="public-main">
        <header class="page-header">
            <p class="breadcrumb"><a href="../index.php">الرئيسية</a><span>/</span>الكليات</p>
            <p class="section-kicker">المجتمع الأكاديمي</p>
            <h1>كليات الجامعة</h1>
            <p>تعرّف على الكليات والقيادات الأكاديمية في جامعة المستقبل.</p>
        </header>

        <section class="content-list" aria-label="قائمة الكليات">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($college = $result->fetch_assoc()): ?>
                    <article class="content-row">
                        <div class="content-row__body">
                            <p class="section-kicker">كلية أكاديمية</p>
                            <h2><?php echo htmlspecialchars($college["name"]); ?></h2>
                            <p><?php echo htmlspecialchars($college["description"]); ?></p>
                        </div>
                        <div class="content-row__meta">
                            <span>عميد الكلية</span>
                            <strong><?php echo htmlspecialchars($college["dean"]); ?></strong>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-state">لا توجد كليات مسجلة حاليًا.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer"><div class="site-footer__inner"><strong>Future University</strong><span>البوابة الجامعية الرسمية</span></div></footer>
    <script src="../script.js"></script>

</body>

</html>