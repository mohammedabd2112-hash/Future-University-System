<?php


require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$sql = "
    SELECT
        students.id,
        students.user_id,
        students.student_number,
        students.college,
        students.department,
        students.level,
        users.name,
        users.email
    FROM students
    LEFT JOIN users
        ON students.user_id = users.id
    ORDER BY students.id DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>إدارة الطلاب</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<header>

    <h1>إدارة طلاب الجامعة</h1>

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

    <h2>الطلاب المسجلون</h2>


    <h3>إضافة طالب جديد</h3>

    <form action="add_student.php" method="POST">

        <label>حساب المستخدم:</label>

        <br>

        <select name="user_id" required>

            <option value="">
                اختر المستخدم
            </option>

            <?php

            $users_result = $conn->query(
                "SELECT id, name, email
                 FROM users
                 WHERE role = 'student'
                 ORDER BY name"
            );

            while ($user = $users_result->fetch_assoc()):

            ?>

                <option value="<?php echo $user["id"]; ?>">

                    <?php
                    echo htmlspecialchars(
                        $user["name"] . " - " . $user["email"]
                    );
                    ?>

                </option>

            <?php endwhile; ?>

        </select>

        <br><br>


        <label>الرقم الجامعي:</label>

        <br>

        <input
            type="text"
            name="student_number"
            required
        >

        <br><br>


        <label>الكلية:</label>

        <br>

        <input
            type="text"
            name="college"
            required
        >

        <br><br>


        <label>القسم:</label>

        <br>

        <input
            type="text"
            name="department"
            required
        >

        <br><br>


        <label>المستوى:</label>

        <br>

        <input
            type="text"
            name="level"
            required
        >

        <br><br>


        <button type="submit">
            إضافة الطالب
        </button>

    </form>


    <hr>


    <h3>قائمة الطلاب</h3>


    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($student = $result->fetch_assoc()): ?>

            <article>

                <h3>
                    <?php echo htmlspecialchars($student["name"] ?? "غير معروف"); ?>
                </h3>

                <p>
                    <strong>البريد:</strong>
                    <?php echo htmlspecialchars($student["email"] ?? ""); ?>
                </p>

                <p>
                    <strong>الرقم الجامعي:</strong>
                    <?php echo htmlspecialchars($student["student_number"]); ?>
                </p>

                <p>
                    <strong>الكلية:</strong>
                    <?php echo htmlspecialchars($student["college"]); ?>
                </p>

                <p>
                    <strong>القسم:</strong>
                    <?php echo htmlspecialchars($student["department"]); ?>
                </p>

                <p>
                    <strong>المستوى:</strong>
                    <?php echo htmlspecialchars($student["level"]); ?>
                </p>


                <a href="edit_student.php?id=<?php echo $student["id"]; ?>">
                    <button type="button">
                        تعديل
                    </button>
                </a>


                <form
                    action="delete_student.php"
                    method="POST"
                    style="display:inline;"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $student["id"]; ?>"
                    >

                    <button
                        type="submit"
                        onclick="return confirm('هل أنت متأكد من حذف هذا الطالب؟');"
                    >
                        حذف
                    </button>

                </form>

            </article>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>
            لا يوجد طلاب مسجلون حاليًا.
        </p>

    <?php endif; ?>

</main>

</body>

</html>
