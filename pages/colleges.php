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
    <title>كليات الجامعة</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <header>
        <h1>جامعة المستقبل</h1>

        <nav>
            <a href="../index.php">الرئيسية</a>
            <a href="colleges.php">الكليات</a>
            <a href="login.html">الطلاب</a>
            <a href="news.php">الأخبار</a>
            <a href="login.html">تسجيل الدخول</a>
        </nav>
    </header>

    <main>

        <h2>كليات الجامعة</h2>

        <?php if ($result->num_rows > 0): ?>

            <?php while ($college = $result->fetch_assoc()): ?>

                <article>

                    <h3>
                        <?php echo htmlspecialchars($college["name"]); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($college["description"]); ?>
                    </p>

                    <p>
                        <strong>عميد الكلية:</strong>
                        <?php echo htmlspecialchars($college["dean"]); ?>
                    </p>

                </article>

                <hr>

            <?php endwhile; ?>

        <?php else: ?>

            <p>لا توجد كليات مسجلة حاليًا.</p>

        <?php endif; ?>

    </main>

</body>

</html>