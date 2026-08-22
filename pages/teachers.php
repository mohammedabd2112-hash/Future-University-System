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
            <a class="is-active" href="teachers.php" aria-current="page">أعضاء هيئة التدريس</a>
            <a href="news.php">الأخبار</a>
            <a class="nav-login" href="login.html">تسجيل الدخول</a>
        </nav>
    </div>
</header>


<main>

    <header class="page-header">
        <p class="breadcrumb"><a href="../index.php">الرئيسية</a><span>/</span>أعضاء هيئة التدريس</p>
        <p class="section-kicker">المجتمع الأكاديمي</p>
        <h1>أعضاء هيئة التدريس</h1>
        <p>تعرف على أعضاء هيئة التدريس والتخصصات الأكاديمية في جامعة المستقبل.</p>
    </header>


    <section class="faculty-grid" aria-label="أعضاء هيئة التدريس">

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($teacher = $result->fetch_assoc()): ?>

                <article class="faculty-member">

                    <div class="faculty-member__identity">
                        <span class="faculty-member__mark" aria-hidden="true">FU</span>
                        <h2><?php echo htmlspecialchars($teacher["name"]); ?></h2>
                    </div>

                    <dl class="faculty-details">
                        <div><dt>التخصص</dt><dd><?php echo htmlspecialchars($teacher["specialization"]); ?></dd></div>
                        <div><dt>الكلية والقسم</dt><dd><?php echo htmlspecialchars($teacher["college"]); ?>، <?php echo htmlspecialchars($teacher["department"]); ?></dd></div>
                        <div><dt>البريد الإلكتروني</dt><dd><?php echo htmlspecialchars($teacher["email"]); ?></dd></div>
                        <div><dt>رقم الهاتف</dt><dd><?php echo htmlspecialchars($teacher["phone"]); ?></dd></div>
                    </dl>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                لا يوجد أعضاء هيئة تدريس حاليًا.
            </p>

        <?php endif; ?>

    </section>

</main>
<footer class="site-footer"><div class="site-footer__inner"><strong>Future University</strong><span>البوابة الجامعية الرسمية</span></div></footer>
<script src="../script.js"></script>

</body>

</html>