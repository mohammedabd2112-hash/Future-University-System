<?php

require_once "../includes/admin_auth.php";
require_once "../config.php";

require_once "../config.php";

$id = intval($_POST["id"] ?? 0);

if ($id <= 0) {
    echo "رقم المستخدم غير صحيح";
    exit;
}


/*
 * منع المدير من حذف حسابه الحالي
 */

if ($id == $_SESSION["user_id"]) {

    echo "لا يمكنك حذف حساب المدير الذي تستخدمه حاليًا";

    exit;
}


$stmt = $conn->prepare(
    "DELETE FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    header("Location: users.php");
    exit;

}


echo "حدث خطأ أثناء حذف المستخدم";


$stmt->close();
$conn->close();

?>
