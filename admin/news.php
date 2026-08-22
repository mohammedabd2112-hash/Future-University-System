<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إدارة الأخبار</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body class="admin-shell admin-inner-page">
<aside class="admin-sidebar"><a class="admin-brand" href="index.php"><span class="brand__mark">FU</span><span><strong>Future University</strong><small>نظام الإدارة</small></span></a><nav class="admin-nav" aria-label="تنقل الإدارة"><a href="index.php"><span>01</span>لوحة التحكم</a><a href="students.php"><span>02</span>الطلاب</a><a href="teachers.php"><span>03</span>أعضاء هيئة التدريس</a><a href="colleges.php"><span>04</span>الكليات</a><a class="is-active" href="news.php" aria-current="page"><span>05</span>الأخبار</a><a href="users.php"><span>06</span>المستخدمون</a></nav><a class="admin-logout" href="../api/logout.php">تسجيل الخروج</a></aside>
<div class="admin-content"><header class="admin-topbar"><div><p class="section-kicker">Administrative system</p><h1>إدارة الأخبار</h1></div><div class="admin-user"><span class="admin-user__mark">FU</span><span>الاتصال الجامعي</span></div></header><main class="admin-main">
<header class="admin-page-heading"><div><p class="breadcrumb"><a href="index.php">لوحة التحكم</a><span>/</span>الأخبار</p><h2>إدارة أخبار الجامعة</h2><p>نشر وتحديث أخبار وإعلانات الجامعة.</p></div><a class="button" href="#add-news">إضافة خبر</a></header>
<section class="admin-form-panel" id="add-news"><h3>إضافة خبر جديد</h3><form class="form-grid" action="add_news.php" method="POST">

    <div class="form-group"><label for="title">عنوان الخبر:</label>

    <input
        type="text"
        id="title"
        name="title"
        required
    ></div>


    <div class="form-group"><label for="content">محتوى الخبر:</label>

    <textarea
        id="content"
        name="content"
        rows="6"
        required
    ></textarea></div>


    <div class="form-group"><label for="status">حالة الخبر:</label>

    <select id="status" name="status">

        <option value="published">
            منشور
        </option>

        <option value="draft">
            مسودة
        </option>

    </select></div>


    <div class="form-actions"><button type="submit">إضافة الخبر</button></div>

    </form></section><section class="admin-table-panel"><div class="section-heading"><div><p class="section-kicker">السجلات</p><h3>الأخبار الحالية</h3></div></div>
    <?php if ($result && $result->num_rows > 0): ?>

        <div class="table-wrap"><table><thead><tr><th>العنوان</th><th>الملخص</th><th>الحالة</th><th>تاريخ الإنشاء</th><th>الإجراءات</th></tr></thead><tbody>
        <?php while ($news = $result->fetch_assoc()): ?><tr>
            <td><?php echo htmlspecialchars($news["title"]); ?></td><td><?php echo htmlspecialchars(mb_strimwidth($news["content"], 0, 100, "...")); ?></td><td><span class="status-info"><?php echo htmlspecialchars($news["status"]); ?></span></td><td><?php echo htmlspecialchars($news["created_at"]); ?></td>
            <td class="table-actions"><a class="button button-secondary" href="edit_news.php?id=<?php echo $news["id"]; ?>">تعديل</a><form class="row-action-form" action="delete_news.php" method="POST"><input type="hidden" name="id" value="<?php echo $news["id"]; ?>"><button class="button-danger" type="submit">حذف</button></form></td>
        </tr><?php endwhile; ?></tbody></table></div>
    <?php else: ?>
        <p class="empty-state">لا توجد أخبار في قاعدة البيانات.</p>
    <?php endif; ?></section>
</main></div>
<script src="../script.js"></script>

</body>

</html>