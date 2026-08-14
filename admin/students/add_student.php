<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../pages/login.html");
    exit;
}

require_once "../config.php";

$user_id = intval($_POST["user_id"] ?? 0);
$student_number = trim($_POST["student_number"] ?? "");
$college = trim($_POST["college"] ?? "");
$department = trim($_POST["department"] ?? "");
$level = trim($_POST["level"] ?? "");

if (
    $user_id <= 0 ||
    $student_number === "" ||
    $college === "" ||
    $department === "" ||
    $level === ""
) {
    echo "يرجى تعبئة جميع البيانات";
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO students
    (user_id, student_number, college, department, level)
    VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "issss",
    $user_id,
    $student_number,
    $college,
    $department,
    $level
);

if ($stmt->execute()) {

    header("Location: students.php");
    exit;

}

echo "حدث خطأ أثناء إضافة الطالب";

$stmt->close();
$conn->close();

?>
