<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo "رقم الكلية غير صحيح";
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, description, dean
     FROM colleges
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "الكلية غير موجودة";
    exit;
}

$college = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الكلية</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body class="admin-edit-page">

<header>
    <h1>تعديل الكلية</h1>
</header>

<main>

    <h2>تعديل بيانات الكلية</h2>

    <form class="admin-edit-form" action="update_college.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $college["id"]; ?>"
        >

        <label>اسم الكلية:</label>
        <br>

        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($college["name"]); ?>"
            required
        >

        <br><br>

        <label>وصف الكلية:</label>
        <br>

        <textarea
            name="description"
            rows="6"
        ><?php echo htmlspecialchars($college["description"]); ?></textarea>

        <br><br>

        <label>عميد الكلية:</label>
        <br>

        <input
            type="text"
            name="dean"
            value="<?php echo htmlspecialchars($college["dean"]); ?>"
        >

        <br><br>

        <button type="submit">
            حفظ التعديلات
        </button>

        <a href="colleges.php">
            إلغاء
        </a>

    </form>

</main>
<script src="../script.js"></script>

</body>

</html>
