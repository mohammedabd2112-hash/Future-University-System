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


$id = intval($_POST["id"] ?? 0);


if ($id <= 0) {
    echo "رقم الخبر غير صحيح";
    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM news WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    header("Location: news.php");
    exit;

} else {

    echo "حدث خطأ أثناء حذف الخبر";

}


$stmt->close();
$conn->close();

?>