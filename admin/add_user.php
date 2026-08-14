<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";


$name = trim($_POST["name"] ?? "");

$email = trim($_POST["email"] ?? "");

$password = $_POST["password"] ?? "";

$phone = trim($_POST["phone"] ?? "");

$role = $_POST["role"] ?? "student";


if ($name === "" || $email === "" || $password === "") {
    echo "يرجى تعبئة البيانات المطلوبة";
    exit;
}


if ($role !== "student" && $role !== "admin") {
    echo "نوع المستخدم غير صحيح";
    exit;
}


/*
 * تشفير كلمة المرور
 */

$hashed_password = password_hash(
    $hashed_password,
    PASSWORD_DEFAULT
);


$stmt = $conn->prepare(
    "INSERT INTO users
    (name, email, password, phone, role)
    VALUES (?, ?, ?, ?, ?)"
);


$stmt->bind_param(
    "sssss",
    $name,
    $email,
    $hashed_password,
    $phone,
    $role
);


if ($stmt->execute()) {

    header("Location: users.php");
    exit;

}


echo "حدث خطأ أثناء إضافة المستخدم";


$stmt->close();

$conn->close();

?>
