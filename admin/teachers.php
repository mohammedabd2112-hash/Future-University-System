<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$result = $conn->query(
    "SELECT id, name, email, phone, college, department, specialization
     FROM teachers
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>أعضاء هيئة التدريس</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<header>

    <h1>إدارة أعضاء هيئة التدريس</h1>

    <nav>

        <a href="index.php">لوحة الإدارة</a>

        <a href="../index.php">الرئيسية</a>

        <a href="../api/logout.php">تسجيل الخروج</a>

    </nav>

</header>


<main>

    <h2>إضافة عضو هيئة تدريس</h2>

    <form action="add_teacher.php" method="POST">

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


        <label>رقم الهاتف:</label>
        <br>

        <input
            type="text"
            name="phone"
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


        <label>التخصص:</label>
        <br>

        <input
            type="text"
            name="specialization"
            required
        >

        <br><br>


        <button type="submit">
            إضافة عضو
        </button>

    </form>


    <hr>


    <h2>أعضاء هيئة التدريس</h2>


    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($teacher = $result->fetch_assoc()): ?>

            <article>

                <h3>
                    <?php echo htmlspecialchars($teacher["name"]); ?>
                </h3>

                <p>
                    <strong>البريد:</strong>
                    <?php echo htmlspecialchars($teacher["email"]); ?>
                </p>

                <p>
                    <strong>الهاتف:</strong>
                    <?php echo htmlspecialchars($teacher["phone"] ?? ""); ?>
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


                <a href="edit_teacher.php?id=<?php echo $teacher["id"]; ?>">

                    <button type="button">
                        تعديل
                    </button>

                </a>


                <form
                    action="delete_teacher.php"
                    method="POST"
                    style="display:inline;"
                >

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo $teacher["id"]; ?>"
                    >

                    <button
                        type="submit"
                        onclick="return confirm('هل أنت متأكد من حذف عضو هيئة التدريس؟');"
                    >
                        حذف
                    </button>

                </form>

            </article>

            <hr>

        <?php endwhile; ?>

    <?php else: ?>

        <p>
            لا يوجد أعضاء هيئة تدريس حاليًا.
        </p>

    <?php endif; ?>

</main>

</body>

</html>
