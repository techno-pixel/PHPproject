<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    // make sure user is a valid admin 
    if(!isset($_SESSION['is_valid_admin'])) {
        header("Location: ./login_form.php");
    }
?>