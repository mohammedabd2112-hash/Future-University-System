<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.html");
    exit;
}

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    exit;
}

require_once "../config.php";

$sql = "
    SELECT id, title, content, status, created_at
    FROM news
    ORDER BY created_at DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>إدارة الأخبار</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<header>

    <h1>لوحة إدارة الأخبار</h1>

    <nav>

        <a href="index.php">لوحة الإدارة</a>

        <a href="../index.php">الرئيسية</a>

        <a href="../pages/news.php">عرض الأخبار</a>

        <a href="../api/logout.php">تسجيل الخروج</a>

    </nav>

</header>


<main>

    <h2>إدارة أخبار الجامعة</h2>

    <p>
        من هنا يستطيع المدير مشاهدة الأخبار الموجودة في النظام.
    </p>


    <h3>الأخبار الحالية</h3>

    <hr>

<h3>إضافة خبر جديد</h3>

<form action="add_news.php" method="POST">

    <label for="title">عنوان الخبر:</label>

    <br>

    <input
        type="text"
        id="title"
        name="title"
        required
    >

    <br><br>


    <label for="content">محتوى الخبر:</label>

    <br>

    <textarea
        id="content"
        name="content"
        rows="6"
        required
    ></textarea>

    <br><br>


    <label for="status">حالة الخبر:</label>

    <br>

    <select id="status" name="status">

        <option value="published">
            منشور
        </option>

        <option value="draft">
            مسودة
        </option>

    </select>

    <br><br>


    <button type="submit">
        إضافة الخبر
    </button>

</form>

<hr>


    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($news = $result->fetch_assoc()): ?>

            <article>

                <h3>
                    <?php echo htmlspecialchars($news["title"]); ?>
                </h3>

                <p>
                    <?php echo htmlspecialchars($news["content"]); ?>
                </p>

                <p>
                    <strong>الحالة:</strong>
                    <?php echo htmlspecialchars($news["status"]); ?>
                </p>

                <small>
                    تاريخ الإنشاء:
                    <?php echo htmlspecialchars($news["created_at"]); ?>
                </small>

                <br><br>

                <a href="edit_news.php?id=<?php echo $news["id"]; ?>">
    <button type="button">
        تعديل
    </button>
</a>


                <form action="delete_news.php" method="POST" style="display:inline;">

    <input
        type="hidden"
        name="id"
        value="<?php echo $news["id"]; ?>"
    >

    <button
        type="submit"
        onclick="return confirm('هل أنت متأكد من حذف هذا الخبر؟');"
    >
        حذف
    </button>

</form>

            </article>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>
            لا توجد أخبار في قاعدة البيانات.
        </p>

    <?php endif; ?>

</main>

</body>

</html>