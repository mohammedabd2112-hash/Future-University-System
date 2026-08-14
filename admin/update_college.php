<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

$name = trim($_POST["name"] ?? "");

$description = trim($_POST["description"] ?? "");

$dean = trim($_POST["dean"] ?? "");


if ($id <= 0) {
    echo "رقم الكلية غير صحيح";
    exit;
}

if ($name === "") {
    echo "يرجى إدخال اسم الكلية";
    exit;
}


$stmt = $conn->prepare(
    "UPDATE colleges
     SET name = ?, description = ?, dean = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "sssi",
    $name,
    $description,
    $dean,
    $id
);


if ($stmt->execute()) {

    header("Location: colleges.php");
    exit;

} else {

    echo "حدث خطأ أثناء تعديل الكلية";

}


$stmt->close();
$conn->close();

?>