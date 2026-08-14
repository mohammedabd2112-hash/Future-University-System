<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

$student_number = trim($_POST["student_number"] ?? "");
$college = trim($_POST["college"] ?? "");
$department = trim($_POST["department"] ?? "");
$level = trim($_POST["level"] ?? "");

if ($id <= 0) {
    echo "رقم الطالب غير صحيح";
    exit;
}

if (
    $student_number === "" ||
    $college === "" ||
    $department === "" ||
    $level === ""
) {
    echo "يرجى تعبئة جميع البيانات";
    exit;
}

$stmt = $conn->prepare(
    "UPDATE students
     SET student_number = ?,
         college = ?,
         department = ?,
         level = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "ssssi",
    $student_number,
    $college,
    $department,
    $level,
    $id
);

if ($stmt->execute()) {

    header("Location: students.php");
    exit;

}

echo "حدث خطأ أثناء تعديل الطالب";

$stmt->close();
$conn->close();

?>
