<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";


$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo "رقم المستخدم غير صحيح";
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, email, phone, role
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "المستخدم غير موجود";
    exit;
}

$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المستخدم</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-edit-page">

<header>
    <h1>تعديل المستخدم</h1>
</header>

<main>

    <h2>تعديل بيانات المستخدم</h2>

    <form class="admin-edit-form" action="update_user.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $user["id"]; ?>"
        >

        <label>الاسم:</label>
        <br>

        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($user["name"]); ?>"
            required
        >

        <br><br>

        <label>البريد الإلكتروني:</label>
        <br>

        <input
            type="email"
            name="email"
            value="<?php echo htmlspecialchars($user["email"]); ?>"
            required
        >

        <br><br>

        <label>رقم الهاتف:</label>
        <br>

        <input
            type="text"
            name="phone"
            value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>"
        >

        <br><br>

        <label>الدور:</label>
        <br>

        <select name="role" required>

            <option
                value="student"
                <?php echo $user["role"] === "student" ? "selected" : ""; ?>
            >
                طالب
            </option>

            <option
                value="admin"
                <?php echo $user["role"] === "admin" ? "selected" : ""; ?>
            >
                مدير
            </option>

        </select>

        <br><br>

        <label>
            كلمة المرور الجديدة
            (اتركها فارغة إذا لم ترد تغييرها):
        </label>

        <br>

        <input
            type="password"
            name="password"
        >

        <br><br>

        <button type="submit">
            حفظ التعديلات
        </button>

        <a href="users.php">
            إلغاء
        </a>

    </form>

</main>
<script src="../script.js"></script>

</body>

</html>