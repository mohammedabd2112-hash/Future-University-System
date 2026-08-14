<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

if ($id <= 0) {
    echo "رقم عضو هيئة التدريس غير صحيح";
    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM teachers
     WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    header("Location: teachers.php");
    exit;

}


echo "حدث خطأ أثناء حذف عضو هيئة التدريس";


$stmt->close();
$conn->close();

?>
