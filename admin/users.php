<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

$sql = "
    SELECT id, name, email, phone, role, created_at
    FROM users
    ORDER BY id DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>إدارة المستخدمين</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<header>

    <h1>إدارة المستخدمين</h1>

    <nav>

        <a href="index.php">
            لوحة الإدارة
        </a>

        <a href="../index.php">
            الرئيسية
        </a>

        <a href="../api/logout.php">
            تسجيل الخروج
        </a>

    </nav>

</header>


<main>

    <h2>إدارة حسابات المستخدمين</h2>


    <!-- إضافة مستخدم -->

    <h3>إضافة مستخدم جديد</h3>

    <form action="add_user.php" method="POST">

        <label>الاسم:</label>

        <br>

        <input
            type="text"
            name="name"
            required
        >

        <br><br>


        <label>البريد الإلكتروني:</label>

        <br>

        <input
            type="email"
            name="email"
            required
        >

        <br><br>


        <label>كلمة المرور:</label>

        <br>

        <input
            type="password"
            name="password"
            required
        >

        <br><br>


        <label>رقم الهاتف:</label>

        <br>

        <input
            type="text"
            name="phone"
        >

        <br><br>


        <label>نوع المستخدم:</label>

        <br>

        <select name="role" required>

            <option value="student">
                طالب
            </option>

            <option value="admin">
                مدير
            </option>

        </select>

        <br><br>


        <button type="submit">
            إضافة المستخدم
        </button>

    </form>


    <hr>


    <!-- المستخدمون -->

    <h3>المستخدمون الحاليون</h3>


    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($user = $result->fetch_assoc()): ?>

            <article>

                <h3>
                    <?php echo htmlspecialchars($user["name"]); ?>
                </h3>

                <p>
                    <strong>البريد:</strong>
                    <?php echo htmlspecialchars($user["email"]); ?>
                </p>

                <p>
                    <strong>الهاتف:</strong>
                    <?php echo htmlspecialchars($user["phone"] ?? ""); ?>
                </p>

                <p>
                    <strong>الدور:</strong>
                    <?php echo htmlspecialchars($user["role"]); ?>
                </p>

                <p>
                    <strong>تاريخ الإنشاء:</strong>
                    <?php echo htmlspecialchars($user["created_at"]); ?>
                </p>


                <a href="edit_user.php?id=<?php echo $user["id"]; ?>">

                    <button type="button">
                        تعديل
                    </button>

                </a>


                <form
                    action="delete_user.php"
                    method="POST"
                    style="display:inline;"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $user["id"]; ?>"
                    >

                    <button
                        type="submit"
                        onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');"
                    >
                        حذف
                    </button>

                </form>

            </article>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>
            لا يوجد مستخدمون.
        </p>

    <?php endif; ?>

</main>

</body>

</html>
