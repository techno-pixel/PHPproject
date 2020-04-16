<?php

    // if a session doesn't exist start one to avoid errors
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    session_destroy(); // clean up session id

    header("Location: ./login_form.php");

?>