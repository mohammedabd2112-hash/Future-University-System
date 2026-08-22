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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة المستخدمين</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body class="admin-shell admin-inner-page">
<aside class="admin-sidebar"><a class="admin-brand" href="index.php"><span class="brand__mark">FU</span><span><strong>Future University</strong><small>نظام الإدارة</small></span></a><nav class="admin-nav" aria-label="تنقل الإدارة"><a href="index.php"><span>01</span>لوحة التحكم</a><a href="students.php"><span>02</span>الطلاب</a><a href="teachers.php"><span>03</span>أعضاء هيئة التدريس</a><a href="colleges.php"><span>04</span>الكليات</a><a href="news.php"><span>05</span>الأخبار</a><a class="is-active" href="users.php" aria-current="page"><span>06</span>المستخدمون</a></nav><a class="admin-logout" href="../api/logout.php">تسجيل الخروج</a></aside>
<div class="admin-content"><header class="admin-topbar"><div><p class="section-kicker">Administrative system</p><h1>إدارة المستخدمين</h1></div><div class="admin-user"><span class="admin-user__mark">FU</span><span>الحسابات والصلاحيات</span></div></header><main class="admin-main">
<header class="admin-page-heading"><div><p class="breadcrumb"><a href="index.php">لوحة التحكم</a><span>/</span>المستخدمون</p><h2>إدارة حسابات المستخدمين</h2><p>إدارة الحسابات وتحديد أدوار الوصول إلى النظام.</p></div><a class="button" href="#add-user">إضافة مستخدم</a></header>
<section class="admin-form-panel" id="add-user"><h3>إضافة مستخدم جديد</h3><form class="form-grid" action="add_user.php" method="POST">

        <div class="form-group"><label for="user-name">الاسم:</label>

        <input
            type="text"
            id="user-name" name="name"
            required
        ></div>


        <div class="form-group"><label for="user-email">البريد الإلكتروني:</label>

        <input
            type="email"
            id="user-email" name="email"
            required
        ></div>


        <div class="form-group"><label for="user-password">كلمة المرور:</label>

        <input
            type="password"
            id="user-password" name="password"
            required
        ></div>


        <div class="form-group"><label for="user-phone">رقم الهاتف:</label>

        <input
            type="text"
            id="user-phone" name="phone"
        ></div>


        <div class="form-group"><label for="user-role">نوع المستخدم:</label>

        <select id="user-role" name="role" required>

            <option value="student">
                طالب
            </option>

            <option value="admin">
                مدير
            </option>

        </select></div>


        <div class="form-actions"><button type="submit">إضافة المستخدم</button></div>

    </form></section><section class="admin-table-panel"><div class="section-heading"><div><p class="section-kicker">السجلات</p><h3>المستخدمون الحاليون</h3></div></div>


    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-wrap"><table><thead><tr><th>الاسم</th><th>البريد</th><th>الهاتف</th><th>الدور</th><th>تاريخ الإنشاء</th><th>الإجراءات</th></tr></thead><tbody>
        <?php while ($user = $result->fetch_assoc()): ?><tr>
            <td><?php echo htmlspecialchars($user["name"]); ?></td><td><?php echo htmlspecialchars($user["email"]); ?></td><td><?php echo htmlspecialchars($user["phone"] ?? ""); ?></td><td><span class="status-info"><?php echo htmlspecialchars($user["role"]); ?></span></td><td><?php echo htmlspecialchars($user["created_at"]); ?></td>
            <td class="table-actions"><a class="button button-secondary" href="edit_user.php?id=<?php echo $user["id"]; ?>">تعديل</a><form class="row-action-form" action="delete_user.php" method="POST"><input type="hidden" name="id" value="<?php echo $user["id"]; ?>"><button class="button-danger" type="submit">حذف</button></form></td>
        </tr>

        <?php endwhile; ?></tbody></table></div>
    <?php else: ?>
        <p class="empty-state">لا يوجد مستخدمون.</p>
    <?php endif; ?></section>
</main></div>
<script src="../script.js"></script>

</body>

</html>
