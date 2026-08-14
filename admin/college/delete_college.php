<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../pages/login.html");
    exit;
}

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

if ($id <= 0) {
    echo "رقم الكلية غير صحيح";
    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM colleges WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    header("Location: colleges.php");
    exit;

} else {

    echo "حدث خطأ أثناء حذف الكلية";

}


$stmt->close();
$conn->close();

?>
