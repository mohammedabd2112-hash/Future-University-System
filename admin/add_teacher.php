<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";


$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$college = trim($_POST["college"] ?? "");
$department = trim($_POST["department"] ?? "");
$specialization = trim($_POST["specialization"] ?? "");


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
    "INSERT INTO teachers
    (name, email, phone, college, department, specialization)
    VALUES (?, ?, ?, ?, ?, ?)"
);


$stmt->bind_param(
    "ssssss",
    $name,
    $email,
    $phone,
    $college,
    $department,
    $specialization
);


if ($stmt->execute()) {

    header("Location: teachers.php");
    exit;

}


echo "حدث خطأ أثناء إضافة عضو هيئة التدريس";


$stmt->close();
$conn->close();

?>
