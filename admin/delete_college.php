<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";
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
