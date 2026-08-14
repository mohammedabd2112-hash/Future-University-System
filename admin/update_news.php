<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";
    exit;
}

require_once "../config.php";


$id = intval($_POST["id"] ?? 0);

$title = trim($_POST["title"] ?? "");

$content = trim($_POST["content"] ?? "");

$status = $_POST["status"] ?? "draft";


if ($id <= 0) {
    echo "رقم الخبر غير صحيح";
    exit;
}


if ($title === "" || $content === "") {
    echo "يرجى إدخال عنوان الخبر ومحتواه";
    exit;
}


if ($status !== "published" && $status !== "draft") {
    $status = "draft";
}


$stmt = $conn->prepare(
    "UPDATE news
     SET title = ?, content = ?, status = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "sssi",
    $title,
    $content,
    $status,
    $id
);


if ($stmt->execute()) {

    header("Location: news.php");
    exit;

} else {

    echo "حدث خطأ أثناء تعديل الخبر";

}


$stmt->close();
$conn->close();

?>
