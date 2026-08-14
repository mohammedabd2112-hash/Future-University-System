<?php

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        "httponly" => true,
        "secure" => false,
        "samesite" => "Lax"
    ]);

    session_start();
}


/* التأكد من تسجيل الدخول */

if (!isset($_SESSION["user_id"])) {

    header("Location: ../pages/login.html");

    exit;
}


/* التأكد أن المستخدم مدير */

if (
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "admin"
) {

    http_response_code(403);

    echo "ليس لديك صلاحية للوصول إلى لوحة الإدارة";

    exit;
}

?>
