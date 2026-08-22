<?php

require_once "../config.php";

$sql = "
    SELECT id, title, content, image, created_at
    FROM news
    WHERE status = 'published'
    ORDER BY created_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أخبار الجامعة</title>

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
                <a href="login.html">الطلاب</a>
                <a href="teachers.php">أعضاء هيئة التدريس</a>
                <a class="is-active" href="news.php" aria-current="page">الأخبار</a>
                <a class="nav-login" href="login.html">تسجيل الدخول</a>
            </nav>
        </div>
    </header>

    <main class="public-main">
        <header class="page-header">
            <p class="breadcrumb"><a href="../index.php">الرئيسية</a><span>/</span>الأخبار</p>
            <p class="section-kicker">المستجدات الجامعية</p>
            <h1>أخبار الجامعة</h1>
            <p>تابع آخر الأخبار والإعلانات المنشورة من جامعة المستقبل.</p>
        </header>

        <section class="news-list news-page-list" aria-label="أخبار الجامعة">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($news = $result->fetch_assoc()): ?>
                    <article class="news-item">
                        <div class="news-item__meta">خبر جامعي <span><?php echo htmlspecialchars($news["created_at"]); ?></span></div>
                        <h2><?php echo htmlspecialchars($news["title"]); ?></h2>
                        <p><?php echo htmlspecialchars($news["content"]); ?></p>
                        <span class="text-link">قراءة الخبر <span aria-hidden="true">←</span></span>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-state">لا توجد أخبار منشورة حاليًا.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer"><div class="site-footer__inner"><strong>Future University</strong><span>البوابة الجامعية الرسمية</span></div></footer>
    <script src="../script.js"></script>

</body>

</html>