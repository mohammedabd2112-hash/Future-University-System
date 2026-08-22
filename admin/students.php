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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة الطلاب</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body class="admin-shell admin-inner-page">
<aside class="admin-sidebar">
    <a class="admin-brand" href="index.php"><span class="brand__mark" aria-hidden="true">FU</span><span><strong>Future University</strong><small>نظام الإدارة</small></span></a>
    <nav class="admin-nav" aria-label="تنقل الإدارة">
        <a href="index.php"><span>01</span>لوحة التحكم</a><a class="is-active" href="students.php" aria-current="page"><span>02</span>الطلاب</a><a href="teachers.php"><span>03</span>أعضاء هيئة التدريس</a><a href="colleges.php"><span>04</span>الكليات</a><a href="news.php"><span>05</span>الأخبار</a><a href="users.php"><span>06</span>المستخدمون</a>
    </nav>
    <a class="admin-logout" href="../api/logout.php">تسجيل الخروج</a>
</aside>
<div class="admin-content">
<header class="admin-topbar"><div><p class="section-kicker">Administrative system</p><h1>إدارة الطلاب</h1></div><div class="admin-user"><span class="admin-user__mark">FU</span><span>الطلاب</span></div></header>
<main class="admin-main">
    <header class="admin-page-heading"><div><p class="breadcrumb"><a href="index.php">لوحة التحكم</a><span>/</span>الطلاب</p><h2>الطلاب المسجلون</h2><p>إدارة البيانات الأكاديمية وحسابات الطلاب.</p></div><a class="button" href="#add-student">إضافة طالب</a></header>
    <section class="admin-form-panel" id="add-student">
    <h3>إضافة طالب جديد</h3>
    <form class="form-grid" action="add_student.php" method="POST">

        <div class="form-group"><label for="student-user">حساب المستخدم:</label>

        <select id="student-user" name="user_id" required>

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

        </select></div>


        <div class="form-group"><label for="student-number">الرقم الجامعي:</label>

        <input
            type="text"
            id="student-number"
            name="student_number"
            required
        ></div>


        <div class="form-group"><label for="student-college">الكلية:</label>

        <input
            type="text"
            id="student-college"
            name="college"
            required
        ></div>


        <div class="form-group"><label for="student-department">القسم:</label>

        <input
            type="text"
            id="student-department"
            name="department"
            required
        ></div>


        <div class="form-group"><label for="student-level">المستوى:</label>

        <input
            type="text"
            id="student-level"
            name="level"
            required
        ></div>


        <div class="form-actions"><button type="submit">إضافة الطالب</button></div>

    </form></section>

    <section class="admin-table-panel"><div class="section-heading"><div><p class="section-kicker">السجلات</p><h3>قائمة الطلاب</h3></div></div>


    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-wrap"><table><thead><tr><th>الاسم</th><th>البريد الإلكتروني</th><th>الرقم الجامعي</th><th>الكلية</th><th>القسم</th><th>المستوى</th><th>الإجراءات</th></tr></thead><tbody>
        <?php while ($student = $result->fetch_assoc()): ?>

            <tr>
                <td><?php echo htmlspecialchars($student["name"] ?? "غير معروف"); ?></td>
                <td><?php echo htmlspecialchars($student["email"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($student["student_number"]); ?></td>
                <td><?php echo htmlspecialchars($student["college"]); ?></td>
                <td><?php echo htmlspecialchars($student["department"]); ?></td>
                <td><?php echo htmlspecialchars($student["level"]); ?></td>
                <td class="table-actions"><a class="button button-secondary" href="edit_student.php?id=<?php echo $student["id"]; ?>">تعديل</a><form class="row-action-form" action="delete_student.php" method="POST"><input type="hidden" name="id" value="<?php echo $student["id"]; ?>"><button class="button-danger" type="submit">حذف</button></form></td>
            </tr>
        <?php endwhile; ?>

        </tbody></table></div>

    <?php else: ?>

        <p>
            لا يوجد طلاب مسجلون حاليًا.
        </p>

    <?php endif; ?></section>
</main></div>
<script src="../script.js"></script>

</body>

</html>
