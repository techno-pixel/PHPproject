<?php
require_once('validate.php');
require_once('file_util.php');




// Get the product data
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

//Retrieve and sanitize the codeinput
$codeInput = filter_input(INPUT_POST, 'code');
$codeInput = filter_var($codeInput, FILTER_SANITIZE_STRING);

//Retrieve and sanitize the name
$name = filter_input(INPUT_POST, 'name');
$name = filter_var($name, FILTER_SANITIZE_STRING);

$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);


$category_error = '';
$code_error = '';
$name_error = '';
$price_error = '';

// Validate inputs
if ($category_id == null || $category_id == false){
    $category_error = "Please choose a category.";
}

if($codeInput == false){
    $code_error = "Please enter a code";
}

if($name == false){
    $name_error = "Please enter a name";
}

if($price == false){
    $price_error = "Please enter a price";
} else if($price < 0 || $price > 50000){
    $price_error = "Please enter a price between 0 and 50 000 dollars";
}

if($price_error!='' || $name_error!=''  || $code_error!=''  || $category_error!='' ) {
    include('add_product_form.php');
    exit();
} else {
    require_once('database.php');

    // Add the product to the database  
    $query = 'INSERT INTO products
                 (categoryID, productCode, productName, listPrice)
              VALUES
                 (:category_id, :code, :name, :price)';
    $statement = $db->prepare($query);
    $statement->bindValue(':category_id', $category_id);
    $statement->bindValue(':code', $codeInput);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':price', $price);
    $statement->execute();
    $errorCode = $statement->errorCode();
    $statement->closeCursor();

    $fileNamez = date("Y-m-d") . ".csv";

    $logPath = getcwd() . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $fileNamez;

    $sep = ",";
    $logMessage = "insert_product.php";
    $logMessage .= $sep . date("Y-m-d H:i:s");
    $logMessage .= $sep . $category_id;
    $logMessage .= $sep . $codeInput;
    $logMessage .= $sep . $name;
    $logMessage .= $sep . $price;


    if($errorCode !=="00000") {
        // something went wrong
        $logMessage .= $sep . "Failure";
    } else {
        // success
        $logMessage .= $sep . "Success";
    }

    file_put_contents($logPath, $logMessage, FILE_APPEND | LOCK_EX);

    // make sure the file exists
    if(isset($_FILES['imageFilez'])) {
        //retrieve filename of the file (what it was named on the client computer)
        $filenames = $_FILES['imageFilez']['name'];
        // if($_FILES['name'] != $codeInput) {
        //     $_FILES['name'] = $codeInput;
        // }
        //make sure file name exists
        if(!empty($filenames)) {
        //if(!empty($filenames) && (substr($filenames, -3) == "png")) { // <--- attempt to see if it ends in png
            // store the temp location of the file when it was uploaded to the server
            $sourceLocation = $_FILES['imageFilez']['tmp_name'];

            //build path to images folder and use same filename as before
            $targetPath = $image_dir_path . DIRECTORY_SEPARATOR . $filenames;

            //move file from temp directory to images folder
            move_uploaded_file($sourceLocation, $targetPath);
        }
    }
    header("Location: ./index.php");

    // Display the Product List page
}
?>