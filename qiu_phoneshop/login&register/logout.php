<?php

session_start();

session_unset();

session_destroy();

header("Location: ../main_page/logged_Index.php");
