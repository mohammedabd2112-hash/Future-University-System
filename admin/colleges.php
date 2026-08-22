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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الكليات</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-shell admin-inner-page">
<aside class="admin-sidebar"><a class="admin-brand" href="index.php"><span class="brand__mark">FU</span><span><strong>Future University</strong><small>نظام الإدارة</small></span></a><nav class="admin-nav" aria-label="تنقل الإدارة"><a href="index.php"><span>01</span>لوحة التحكم</a><a href="students.php"><span>02</span>الطلاب</a><a href="teachers.php"><span>03</span>أعضاء هيئة التدريس</a><a class="is-active" href="colleges.php" aria-current="page"><span>04</span>الكليات</a><a href="news.php"><span>05</span>الأخبار</a><a href="users.php"><span>06</span>المستخدمون</a></nav><a class="admin-logout" href="../api/logout.php">تسجيل الخروج</a></aside>
<div class="admin-content"><header class="admin-topbar"><div><p class="section-kicker">Administrative system</p><h1>إدارة الكليات</h1></div><div class="admin-user"><span class="admin-user__mark">FU</span><span>الإدارة الأكاديمية</span></div></header><main class="admin-main">
<header class="admin-page-heading"><div><p class="breadcrumb"><a href="index.php">لوحة التحكم</a><span>/</span>الكليات</p><h2>إدارة الكليات</h2><p>إدارة الكليات والقيادات الأكاديمية المسجلة.</p></div><a class="button" href="#add-college">إضافة كلية</a></header>
<section class="admin-form-panel" id="add-college"><h3>إضافة كلية جديدة</h3><form class="form-grid" action="add_college.php" method="POST">

        <div class="form-group"><label for="college-name">اسم الكلية:</label>

        <input id="college-name" type="text" name="name" required></div>

        <div class="form-group"><label for="college-description">وصف الكلية:</label>

        <textarea id="college-description" name="description" rows="5"></textarea></div>

        <div class="form-group"><label for="college-dean">عميد الكلية:</label>

        <input id="college-dean" type="text" name="dean"></div>

        <div class="form-actions"><button type="submit">إضافة الكلية</button></div>

    </form></section><section class="admin-table-panel"><div class="section-heading"><div><p class="section-kicker">السجلات</p><h3>الكليات الحالية</h3></div></div>

    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-wrap"><table><thead><tr><th>اسم الكلية</th><th>الوصف</th><th>العميد</th><th>تاريخ الإنشاء</th><th>الإجراءات</th></tr></thead><tbody>
        <?php while ($college = $result->fetch_assoc()): ?><tr>
            <td><?php echo htmlspecialchars($college["name"]); ?></td><td><?php echo htmlspecialchars($college["description"]); ?></td><td><?php echo htmlspecialchars($college["dean"]); ?></td><td><?php echo htmlspecialchars($college["created_at"]); ?></td>
            <td class="table-actions"><a class="button button-secondary" href="edit_college.php?id=<?php echo $college["id"]; ?>">تعديل</a><form class="row-action-form" action="delete_college.php" method="POST"><input type="hidden" name="id" value="<?php echo $college["id"]; ?>"><button class="button-danger" type="submit">حذف</button></form></td>
        </tr>

        <?php endwhile; ?></tbody></table></div>
    <?php else: ?>
        <p class="empty-state">لا توجد كليات حاليًا.</p>
    <?php endif; ?></section>
</main></div>
<script src="../script.js"></script>

</body>
</html>