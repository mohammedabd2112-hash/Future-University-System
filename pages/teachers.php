<?php

require_once "../config.php";

$sql = "
    SELECT
        id,
        name,
        email,
        phone,
        college,
        department,
        specialization
    FROM teachers
    ORDER BY id DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>أعضاء هيئة التدريس - جامعة المستقبل</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<header>

    <h1>🎓 جامعة المستقبل</h1>

    <nav>

        <a href="../index.php">الرئيسية</a>

        <a href="colleges.php">الكليات</a>

        <a href="students.php">الطلاب</a>

        <a href="teachers.php">أعضاء هيئة التدريس</a>

        <a href="news.php">الأخبار</a>

        <a href="login.html">تسجيل الدخول</a>

    </nav>

</header>


<main>

    <section class="hero">

        <h2>أعضاء هيئة التدريس</h2>

        <p>
            تعرف على أعضاء هيئة التدريس والتخصصات الأكاديمية
            في جامعة المستقبل.
        </p>

    </section>


    <section class="cards">

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($teacher = $result->fetch_assoc()): ?>

                <article class="card">

                    <h3>
                        👨‍🏫
                        <?php echo htmlspecialchars($teacher["name"]); ?>
                    </h3>

                    <p>
                        <strong>البريد الإلكتروني:</strong>
                        <?php echo htmlspecialchars($teacher["email"]); ?>
                    </p>

                    <p>
                        <strong>رقم الهاتف:</strong>
                        <?php echo htmlspecialchars($teacher["phone"]); ?>
                    </p>

                    <p>
                        <strong>الكلية:</strong>
                        <?php echo htmlspecialchars($teacher["college"]); ?>
                    </p>

                    <p>
                        <strong>القسم:</strong>
                        <?php echo htmlspecialchars($teacher["department"]); ?>
                    </p>

                    <p>
                        <strong>التخصص:</strong>
                        <?php echo htmlspecialchars($teacher["specialization"]); ?>
                    </p>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                لا يوجد أعضاء هيئة تدريس حاليًا.
            </p>

        <?php endif; ?>

    </section>

</main>

</body>

</html>