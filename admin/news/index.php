<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.html");
    exit;
}

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo "ليس لديك صلاحية للوصول إلى لوحة الإدارة";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>لوحة الإدارة</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

    <header>

        <h1>لوحة إدارة جامعة المستقبل</h1>

        <nav>

            <a href="../index.php">
                الرئيسية
            </a>

            <a href="../pages/news.php">
                الأخبار
            </a>

            <a href="../pages/colleges.php">
                الكليات
            </a>

            <a href="../pages/students.php">
                الطلاب
            </a>

            <a href="../api/logout.php">
                تسجيل الخروج
            </a>

        </nav>

    </header>


    <main>

        <h2>
            مرحبًا،
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </h2>

        <p>
            أنت الآن داخل لوحة الإدارة.
        </p>

        <h2>إدارة النظام</h2>

        <div>

            <h3>👨‍🎓 إدارة الطلاب</h3>

            <p>
                إضافة وتعديل وحذف بيانات الطلاب.
            </p>

        </div>

        <div>

            <h3>📰 إدارة الأخبار</h3>

            <p>
                إضافة وتعديل وحذف أخبار الجامعة.
            </p>

        </div>

        <div>

            <h3>🏫 إدارة الكليات</h3>

            <p>
                إضافة وتعديل وحذف الكليات.
            </p>

        </div>

    </main>

</body>

</html>