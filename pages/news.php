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
    <title>أخبار الجامعة</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <header>

        <h1>جامعة المستقبل</h1>

        <nav>
            <a href="../index.html">الرئيسية</a>
            <a href="colleges.html">الكليات</a>
            <a href="students.php">الطلاب</a>
            <a href="news.php">الأخبار</a>
            <a href="login.html">تسجيل الدخول</a>
        </nav>

    </header>

    <main>

        <h2>أخبار الجامعة</h2>

        <?php if ($result->num_rows > 0): ?>

            <?php while ($news = $result->fetch_assoc()): ?>

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

</body>

</html>