<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../pages/login.html");
    exit;
}

require_once "../config.php";

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");
$dean = trim($_POST["dean"] ?? "");

if ($name === "") {
    echo "يرجى إدخال اسم الكلية";
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO colleges (name, description, dean)
     VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "sss",
    $name,
    $description,
    $dean
);

if ($stmt->execute()) {
    header("Location: colleges.php");
    exit;
}

echo "حدث خطأ أثناء إضافة الكلية";

$stmt->close();
$conn->close();

?>
