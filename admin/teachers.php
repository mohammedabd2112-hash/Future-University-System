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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>أعضاء هيئة التدريس</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body class="admin-shell admin-inner-page">
<aside class="admin-sidebar"><a class="admin-brand" href="index.php"><span class="brand__mark">FU</span><span><strong>Future University</strong><small>نظام الإدارة</small></span></a><nav class="admin-nav" aria-label="تنقل الإدارة"><a href="index.php"><span>01</span>لوحة التحكم</a><a href="students.php"><span>02</span>الطلاب</a><a class="is-active" href="teachers.php" aria-current="page"><span>03</span>أعضاء هيئة التدريس</a><a href="colleges.php"><span>04</span>الكليات</a><a href="news.php"><span>05</span>الأخبار</a><a href="users.php"><span>06</span>المستخدمون</a></nav><a class="admin-logout" href="../api/logout.php">تسجيل الخروج</a></aside>
<div class="admin-content"><header class="admin-topbar"><div><p class="section-kicker">Administrative system</p><h1>أعضاء هيئة التدريس</h1></div><div class="admin-user"><span class="admin-user__mark">FU</span><span>الإدارة الأكاديمية</span></div></header>
<main class="admin-main">
    <header class="admin-page-heading"><div><p class="breadcrumb"><a href="index.php">لوحة التحكم</a><span>/</span>أعضاء هيئة التدريس</p><h2>أعضاء هيئة التدريس</h2><p>إدارة أعضاء هيئة التدريس والتخصصات الأكاديمية.</p></div><a class="button" href="#add-teacher">إضافة عضو</a></header>
    <section class="admin-form-panel" id="add-teacher"><h3>إضافة عضو هيئة تدريس</h3><form class="form-grid" action="add_teacher.php" method="POST">

        <div class="form-group"><label for="teacher-name">الاسم:</label>

        <input
            type="text"
            id="teacher-name" name="name"
            required
        ></div>


        <div class="form-group"><label for="teacher-email">البريد الإلكتروني:</label>

        <input
            type="email"
            id="teacher-email" name="email"
            required
        ></div>


        <div class="form-group"><label for="teacher-phone">رقم الهاتف:</label>

        <input
            type="text"
            id="teacher-phone" name="phone"
        ></div>


        <div class="form-group"><label for="teacher-college">الكلية:</label>

        <input
            type="text"
            id="teacher-college" name="college"
            required
        ></div>


        <div class="form-group"><label for="teacher-department">القسم:</label>

        <input
            type="text"
            id="teacher-department" name="department"
            required
        ></div>


        <div class="form-group"><label for="teacher-specialization">التخصص:</label>

        <input
            type="text"
            id="teacher-specialization" name="specialization"
            required
        ></div>


        <div class="form-actions"><button type="submit">إضافة عضو</button></div>

    </form></section><section class="admin-table-panel"><div class="section-heading"><div><p class="section-kicker">السجلات</p><h3>أعضاء هيئة التدريس</h3></div></div>


    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-wrap"><table><thead><tr><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>الكلية</th><th>القسم</th><th>التخصص</th><th>الإجراءات</th></tr></thead><tbody>
        <?php while ($teacher = $result->fetch_assoc()): ?><tr>
            <td><?php echo htmlspecialchars($teacher["name"]); ?></td><td><?php echo htmlspecialchars($teacher["email"]); ?></td><td><?php echo htmlspecialchars($teacher["phone"] ?? ""); ?></td><td><?php echo htmlspecialchars($teacher["college"]); ?></td><td><?php echo htmlspecialchars($teacher["department"]); ?></td><td><?php echo htmlspecialchars($teacher["specialization"]); ?></td>
            <td class="table-actions"><a class="button button-secondary" href="edit_teacher.php?id=<?php echo $teacher["id"]; ?>">تعديل</a><form class="row-action-form" action="delete_teacher.php" method="POST"><input type="hidden" name="id" value="<?php echo $teacher["id"]; ?>"><button class="button-danger" type="submit">حذف</button></form></td>
        </tr>

        <?php endwhile; ?></tbody></table></div>
    <?php else: ?>
        <p class="empty-state">لا يوجد أعضاء هيئة تدريس حاليًا.</p>
    <?php endif; ?></section>
</main></div>
<script src="../script.js"></script>

</body>

</html>
