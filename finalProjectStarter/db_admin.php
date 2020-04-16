<?php

function is_valid_admin_login($email, $password) {
    global $db;
    $query = 'SELECT password FROM administrators
              WHERE emailAddress = :email';
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();
    $row = $statement->fetch();
    $statement->closeCursor();
    $hash = $row['password'];
    return password_verify($password, $hash);
}

function email_exists($email) {
    global $db;
    $query =
        'SELECT * FROM administrators
        WHERE emailAddress = :email';
    $statement = $db->prepare($query);
    $statement->bindValue(':email', $email);
    $statement->execute();

    return $statement->rowCount() > 0;
}

function add_admin($email, $password, $first_name, $last_name) {
    $email = strtolower($email);
    if(email_exists($email)) {

        return "Email already exists. User not created.";

    } else {
        global $db;
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query =
            'INSERT INTO administrators (emailAddress, password, firstName, lastName)
             VALUES (:email, :password, :firstName, :lastName)';
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hash);
        $statement->bindValue(':firstName', $first_name);
        $statement->bindValue(':lastName', $last_name);
        $statement->execute();
        $statement->closeCursor();

        return "User created";
    }
}

?>