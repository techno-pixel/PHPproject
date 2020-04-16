<?php

    //Check to see if there is no session yet
    if (session_status() == PHP_SESSION_NONE) {
        //Start the session since there is no session
        session_start();
    }

    // get the data from the form
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password');

    //Retrieve and sanitize the first and last names
    $first_name = filter_input(INPUT_POST, 'first_name');
    $first_name = filter_var($first_name, FILTER_SANITIZE_STRING);

    //Retrieve and sanitize the first and last names
    $last_name = filter_input(INPUT_POST, 'last_name');
    $last_name = filter_var($last_name, FILTER_SANITIZE_STRING);

    //Clearing our the messages from the session if they already exist
    unset($_SESSION['email_message']);
    unset($_SESSION['password_message']);
    unset($_SESSION['login_message']);
    
    //Validate email
    if ( $email === FALSE ) {
        $_SESSION['email_message'] = 'Please provide a valid email address.';
    }

    //validate password
    if ( $password === FALSE || strlen($password) < 6 ) {
        $_SESSION['password_message'] = 'Please provide a password at least 6 characters long.';
    }

    // if an error message exists, go to the index page
    if (isset($_SESSION['password_message']) ||
        isset($_SESSION['email_message']) ) {

        $_SESSION['email'] = $email;

        //We could use include('index.php'); or header('Location: ./index.php'); here
        header('Location: ./login_form.php');
        exit();
    } else {
        require_once('database.php');
        require_once('db_admin.php');
        //Here is where we need to validate the user and set the session
        if(is_valid_admin_login($email, $password)) {
            echo 'Login Successful';
            $_SESSION['is_valid_admin'] = true;
            header('Location: ./index.php');
        } else {
            if(email_exists($email)) {
                $_SESSION['login_message'] = "Password is incorrect. Please try again.";
            } else {
                $_SESSION['login_message'] = "Username does not exist. Please try again.";
            }
            header('Location: ./login_form.php');
            exit();
        }
    }

?>
