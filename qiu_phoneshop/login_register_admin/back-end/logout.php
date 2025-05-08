<?php

session_start();

session_unset();

session_destroy();

foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 3600, '/');
    unset($_COOKIE[$key]);
}

header("Location: ../front-end/admin_login.html");
