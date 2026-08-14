<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    exit;
}

require_once "../config.php";


$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");
$status = $_POST["status"] ?? "draft";


if ($title === "" || $content === "") {
    echo "يرجى إدخال عنوان الخبر ومحتواه";
    exit;
}


if ($status !== "published" && $status !== "draft") {
    $status = "draft";
}


$stmt = $conn->prepare(
    "INSERT INTO news (title, content, status)
     VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "sss",
    $title,
    $content,
    $status
);


if ($stmt->execute()) {

    header("Location: news.php");
    exit;

} else {

    echo "حدث خطأ أثناء إضافة الخبر";

}


$stmt->close();
$conn->close();

?>
