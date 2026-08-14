<?php

session_start();

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$college = trim($_POST["college"] ?? "");
$department = trim($_POST["department"] ?? "");
$specialization = trim($_POST["specialization"] ?? "");


if ($id <= 0) {
    echo "رقم عضو هيئة التدريس غير صحيح";
    exit;
}


if (
    $name === "" ||
    $email === "" ||
    $college === "" ||
    $department === "" ||
    $specialization === ""
) {

    echo "يرجى تعبئة جميع البيانات المطلوبة";
    exit;

}


$stmt = $conn->prepare(
    "UPDATE teachers
     SET name = ?,
         email = ?,
         phone = ?,
         college = ?,
         department = ?,
         specialization = ?
     WHERE id = ?"
);


$stmt->bind_param(
    "ssssssi",
    $name,
    $email,
    $phone,
    $college,
    $department,
    $specialization,
    $id
);


if ($stmt->execute()) {

    header("Location: teachers.php");
    exit;

}


echo "حدث خطأ أثناء تعديل عضو هيئة التدريس";


$stmt->close();
$conn->close();

?>
