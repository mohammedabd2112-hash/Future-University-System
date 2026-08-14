<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.html");
    exit;
}

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    exit;
}

require_once "../config.php";


$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    echo "رقم الخبر غير صحيح";
    exit;
}


$stmt = $conn->prepare(
    "SELECT id, title, content, status
     FROM news
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "الخبر غير موجود";
    exit;
}

$news = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <title>تعديل الخبر</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<header>

    <h1>تعديل الخبر</h1>

</header>


<main>

    <h2>تعديل بيانات الخبر</h2>


    <form action="update_news.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $news["id"]; ?>"
        >


        <label>
            عنوان الخبر:
        </label>

        <br>

        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($news["title"]); ?>"
            required
        >

        <br><br>


        <label>
            محتوى الخبر:
        </label>

        <br>

        <textarea
            name="content"
            rows="7"
            required
        ><?php echo htmlspecialchars($news["content"]); ?></textarea>

        <br><br>


        <label>
            حالة الخبر:
        </label>

        <br>

        <select name="status">

            <option
                value="published"
                <?php echo $news["status"] === "published" ? "selected" : ""; ?>
            >
                منشور
            </option>

            <option
                value="draft"
                <?php echo $news["status"] === "draft" ? "selected" : ""; ?>
            >
                مسودة
            </option>

        </select>

        <br><br>


        <button type="submit">
            حفظ التعديلات
        </button>


        <a href="news.php">
            إلغاء
        </a>

    </form>

</main>

</body>

</html>
