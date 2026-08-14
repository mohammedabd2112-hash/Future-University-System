<?php

require_once "config.php";

/* جلب أحدث 3 أخبار */
$news_sql = "
    SELECT id, title, content, created_at
    FROM news
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 3
";

$news_result = $conn->query($news_sql);


/* حساب عدد الكليات */
$colleges_sql = "
    SELECT COUNT(*) AS total
    FROM colleges
";

$colleges_result = $conn->query($colleges_sql);

$colleges_count = 0;

if ($colleges_result) {
    $college_data = $colleges_result->fetch_assoc();
    $colleges_count = $college_data["total"];
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>جامعة المستقبل</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- رأس الموقع -->
    <header>

        <h1>جامعة المستقبل</h1>

        <nav>
            <a href="index.php">الرئيسية</a>
            <a href="pages/colleges.php">الكليات</a>
            <a href="pages/login.html">الطلاب</a>
            <a href="pages/teachers.php">أعضاء هيئة التدريس</a>
            <a href="pages/news.php">الأخبار</a>
            <a href="pages/login.html">تسجيل الدخول</a>
        </nav>

    </header>


    <!-- الصفحة الرئيسية -->
    <main>

        <h2>مرحباً بكم في موقع جامعة المستقبل</h2>

        <p>
            الموقع الإلكتروني الرسمي للجامعة
        </p>

        <button onclick="welcomeMessage()">
            اضغط هنا
        </button>


        <!-- إحصائيات الجامعة -->

        <h2>إحصائيات الجامعة</h2>

        <p>
            عدد الكليات:
            <strong><?php echo $colleges_count; ?></strong>
        </p>


        <!-- أحدث الأخبار -->

        <h2>أحدث الأخبار</h2>

        <?php if ($news_result && $news_result->num_rows > 0): ?>

            <?php while ($news = $news_result->fetch_assoc()): ?>

                <article>

                    <h3>
                        <?php echo htmlspecialchars($news["title"]); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($news["content"]); ?>
                    </p>

                    <small>
                        تاريخ النشر:
                        <?php echo htmlspecialchars($news["created_at"]); ?>
                    </small>

                </article>

                <hr>

            <?php endwhile; ?>

        <?php else: ?>

            <p>لا توجد أخبار منشورة حاليًا.</p>

        <?php endif; ?>

    </main>


    <!-- JavaScript -->
    <script src="script.js"></script>

</body>

</html>