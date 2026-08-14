<?php

session_start();

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

$name = trim($_POST["name"] ?? "");

$email = trim($_POST["email"] ?? "");

$phone = trim($_POST["phone"] ?? "");

$role = $_POST["role"] ?? "student";

$password = $_POST["password"] ?? "";


if ($id <= 0) {
    echo "رقم المستخدم غير صحيح";
    exit;
}

if ($name === "" || $email === "") {
    echo "يرجى إدخال الاسم والبريد الإلكتروني";
    exit;
}

if ($role !== "student" && $role !== "admin") {
    echo "الدور غير صحيح";
    exit;
}


/*
 * إذا كتب المدير كلمة مرور جديدة
 */

if ($password !== "") {

    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $stmt = $conn->prepare(
        "UPDATE users
         SET name = ?,
             email = ?,
             phone = ?,
             role = ?,
             password = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "sssssi",
        $name,
        $email,
        $phone,
        $role,
        $hashed_password,
        $id
    );

} else {

    $stmt = $conn->prepare(
        "UPDATE users
         SET name = ?,
             email = ?,
             phone = ?,
             role = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "ssssi",
        $name,
        $email,
        $phone,
        $role,
        $id
    );
}


if ($stmt->execute()) {

    /*
     * إذا قام المدير بتعديل حسابه نفسه،
     * نحدث Session أيضًا.
     */

    if ($_SESSION["user_id"] == $id) {

        $_SESSION["user_name"] = $name;
        $_SESSION["user_role"] = $role;

    }

    header("Location: users.php");
    exit;

}


echo "حدث خطأ أثناء تعديل المستخدم";


$stmt->close();
$conn->close();

?>
