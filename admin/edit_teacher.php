<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo "رقم عضو هيئة التدريس غير صحيح";
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, email, phone, college, department, specialization
     FROM teachers
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "عضو هيئة التدريس غير موجود";
    exit;
}

$teacher = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل عضو هيئة التدريس</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-edit-page">

<header>

    <h1>تعديل عضو هيئة التدريس</h1>

</header>

<main>

    <h2>تعديل البيانات</h2>

    <form class="admin-edit-form" action="update_teacher.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $teacher["id"]; ?>"
        >

        <label>الاسم:</label>
        <br>

        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($teacher["name"]); ?>"
            required
        >

        <br><br>

        <label>البريد الإلكتروني:</label>
        <br>

        <input
            type="email"
            name="email"
            value="<?php echo htmlspecialchars($teacher["email"]); ?>"
            required
        >

        <br><br>

        <label>رقم الهاتف:</label>
        <br>

        <input
            type="text"
            name="phone"
            value="<?php echo htmlspecialchars($teacher["phone"] ?? ""); ?>"
        >

        <br><br>

        <label>الكلية:</label>
        <br>

        <input
            type="text"
            name="college"
            value="<?php echo htmlspecialchars($teacher["college"]); ?>"
            required
        >

        <br><br>

        <label>القسم:</label>
        <br>

        <input
            type="text"
            name="department"
            value="<?php echo htmlspecialchars($teacher["department"]); ?>"
            required
        >

        <br><br>

        <label>التخصص:</label>
        <br>

        <input
            type="text"
            name="specialization"
            value="<?php echo htmlspecialchars($teacher["specialization"]); ?>"
            required
        >

        <br><br>

        <button type="submit">
            حفظ التعديلات
        </button>

        <a href="teachers.php">
            إلغاء
        </a>

    </form>

</main>
<script src="../script.js"></script>

</body>

</html>
