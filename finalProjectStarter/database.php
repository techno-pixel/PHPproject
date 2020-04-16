<?php
    $dsn = 'mysql:host=localhost:3307;dbname=my_guitar_shop1';
    $username = 'mgs_user';
    $dbpassword = 'pa55word';

    try {
        $db = new PDO($dsn, $username, $dbpassword);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('database_error.php');
        exit();
    }
?>