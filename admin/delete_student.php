<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";
require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

if ($id <= 0) {
    echo "رقم الطالب غير صحيح";
    exit;
}

$stmt = $conn->prepare(
    "DELETE FROM students WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: students.php");
    exit;

}

echo "حدث خطأ أثناء حذف الطالب";

$stmt->close();
$conn->close();

?>
