<?php

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        "httponly" => true,
        "secure" => false,
        "samesite" => "Lax"
    ]);

    session_start();
}


if (!isset($_SESSION["user_id"])) {

    header("Location: login.html");

    exit;
}


if (
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "student"
) {

    http_response_code(403);

    echo "ليس لديك صلاحية للوصول إلى هذه الصفحة";

    exit;
}

?>
