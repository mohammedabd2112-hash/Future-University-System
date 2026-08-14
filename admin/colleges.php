<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";


if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    exit;
}

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
    <title>إدارة الكليات</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<header>

    <h1>إدارة كليات الجامعة</h1>

    <nav>
        <a href="index.php">لوحة الإدارة</a>
        <a href="../index.php">الرئيسية</a>
        <a href="../pages/colleges.php">عرض الكليات</a>
        <a href="../api/logout.php">تسجيل الخروج</a>
    </nav>

</header>

<main>

    <h2>إدارة الكليات</h2>

    <h3>إضافة كلية جديدة</h3>

    <form action="add_college.php" method="POST">

        <label>اسم الكلية:</label>
        <br>

        <input type="text" name="name" required>

        <br><br>

        <label>وصف الكلية:</label>
        <br>

        <textarea name="description" rows="5"></textarea>

        <br><br>

        <label>عميد الكلية:</label>
        <br>

        <input type="text" name="dean">

        <br><br>

        <button type="submit">
            إضافة الكلية
        </button>

    </form>

    <hr>

    <h3>الكليات الحالية</h3>

    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($college = $result->fetch_assoc()): ?>

            <article>

                <h3>
                    <?php echo htmlspecialchars($college["name"]); ?>
                </h3>

                <p>
                    <?php echo htmlspecialchars($college["description"]); ?>
                </p>

                <p>
                    <strong>العميد:</strong>
                    <?php echo htmlspecialchars($college["dean"]); ?>
                </p>

                <a href="edit_college.php?id=<?php echo $college["id"]; ?>">
                    <button type="button">تعديل</button>
                </a>

                <form
                    action="delete_college.php"
                    method="POST"
                    style="display:inline;"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $college["id"]; ?>"
                    >

                    <button
                        type="submit"
                        onclick="return confirm('هل أنت متأكد من حذف هذه الكلية؟');"
                    >
                        حذف
                    </button>

                </form>

            </article>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>لا توجد كليات حاليًا.</p>

    <?php endif; ?>

</main>

</body>
</html>