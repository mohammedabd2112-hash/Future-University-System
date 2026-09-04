<?php
require_once "config.php";

$new_password = password_hash("123456", PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = ? WHERE email = 'admin@gmail.com'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $new_password);

if ($stmt->execute()) {
    echo "تم تحديث كلمة المرور لحساب admin@gmail.com إلى 123456 بنجاح ✅";
} else {
    echo "حدث خطأ أثناء التحديث: " . $conn->error;
}

$stmt->close();
$conn->close();
?>