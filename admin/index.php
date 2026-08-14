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

<body>

<header>

    <h1>🎓 لوحة إدارة جامعة المستقبل</h1>

    <nav>

        <a href="../index.php">
            الرئيسية
        </a>

        <a href="students.php">
            الطلاب
        </a>

        <a href="teachers.php">
            أعضاء هيئة  التدريس
        </a>

        <a href="colleges.php">
            الكليات
        </a>

        <a href="news.php">
            الأخبار
        </a>

        <a href="users.php">
            المستخدمون
        </a>

        <a href="../api/logout.php">
            تسجيل الخروج
        </a>

    </nav>

</header>


<main>

    <section class="hero">

        <h2>
            مرحبًا،
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </h2>

        <p>
            أنت الآن داخل لوحة الإدارة الخاصة بجامعة المستقبل
        </p>

    </section>


    <h2>
        إدارة النظام
    </h2>


    <section class="cards">


        <!-- الطلاب -->

        <div class="card">

            <h3>👨‍🎓 إدارة الطلاب</h3>

            <p>
                إضافة وتعديل وحذف بيانات الطلاب
                وإدارة معلوماتهم الأكاديمية.
            </p>

            <br>

            <a href="students.php">

                <button type="button">
                    إدارة الطلاب
                </button>

            </a>

        </div>


        <!-- أعضاء هيئة التدريس -->

        <div class="card">

            <h3>👨‍🏫 أعضاء هيئة التدريس</h3>

            <p>
                إدارة بيانات أعضاء هيئة التدريس
                والتخصصات الأكاديمية.
            </p>

            <br>

            <a href="teachers.php">

                <button type="button">
                    إدارة أعضاء هيئة التدريس
                </button>

            </a>

        </div>


        <!-- الكليات -->

        <div class="card">

            <h3>🏫 إدارة الكليات</h3>

            <p>
                إضافة وتعديل وحذف الكليات
                والأقسام التابعة لها.
            </p>

            <br>

            <a href="colleges.php">

                <button type="button">
                    إدارة الكليات
                </button>

            </a>

        </div>


        <!-- الأخبار -->

        <div class="card">

            <h3>📰 إدارة الأخبار</h3>

            <p>
                نشر وتعديل وحذف أخبار وإعلانات
                الجامعة.
            </p>

            <br>

            <a href="news.php">

                <button type="button">
                    إدارة الأخبار
                </button>

            </a>

        </div>


        <!-- المستخدمون -->

        <div class="card">

            <h3>👤 إدارة المستخدمين</h3>

            <p>
                إدارة حسابات المستخدمين وتحديد
                صلاحياتهم داخل النظام.
            </p>

            <br>

            <a href="users.php">

                <button type="button">
                    إدارة المستخدمين
                </button>

            </a>

        </div>


    </section>

</main>

</body>

</html>