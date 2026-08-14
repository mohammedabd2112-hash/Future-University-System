<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "loggedIn" => false,
        "message" => "المستخدم غير مسجل الدخول"
    ]);

    exit;
}

echo json_encode([
    "loggedIn" => true,
    "user" => [
        "id" => $_SESSION["user_id"],
        "name" => $_SESSION["user_name"],
        "email" => $_SESSION["user_email"],
        "role" => $_SESSION["user_role"]
    ]
]);

?>