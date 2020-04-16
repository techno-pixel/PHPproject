<?php
require_once('validate.php');
require_once('file_util.php');
require_once('database.php');

// Get category ID
if (!isset($category_id)) {
  $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
  if ($category_id == NULL || $category_id == FALSE) {
      $category_id = 1;
  }
}

// Get name for selected category
$queryCategory = 'SELECT * FROM categories
                      WHERE categoryID = :category_id';
$statement1 = $db->prepare($queryCategory);
$statement1->bindValue(':category_id', $category_id);
$statement1->execute();
$category = $statement1->fetch();
$category_name = $category['categoryName'];
$statement1->closeCursor();

// Get all categories
$queryAllCategories = 'SELECT * FROM categories
                           ORDER BY categoryID';
$statement2 = $db->prepare($queryAllCategories);
$statement2->execute();
$categories = $statement2->fetchAll();
$statement2->closeCursor();

// Get products for selected category
$queryProducts = 'SELECT * FROM products
              WHERE categoryID = :category_id
              ORDER BY productID';
$statement3 = $db->prepare($queryProducts);
$statement3->bindValue(':category_id', $category_id);
$statement3->execute();
$products = $statement3->fetchAll();
$statement3->closeCursor();

?>
<!DOCTYPE html>
<html>
<!-- the head section -->
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <title>My Guitar Shop</title>
    <link rel="stylesheet" type="text/css" href="main.css" />
</head>

<!-- the body section -->
<body>
<main>
    <h1>Product List</h1>
    <aside>
            <!-- display a list of categories -->
            <h2>Categories</h2>

            <nav>
            <ul>
                <?php foreach ($categories as $category) : ?>
                <li><a class="btn btn-block
                  <?php if($category['categoryID']==$category_id) {
                           echo "btn-success";
                        } else {
                           echo "btn-outline-success";
                        } ?>"
                        href=".?category_id=<?php echo $category['categoryID']; ?>">
                        <?php echo $category['categoryName']; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            </nav>
        </aside>

    <section>
        <!-- display a table of products -->
        <h2><?php echo $category_name; ?></h2>
        <table class="table table-striped">
          <thead>
              <tr>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Image</th>
                  <th class="right">Price</th>
              </tr>
          </thead>
          <tbody>
              <?php foreach ($products as $product) : ?>
              <tr>
                  <td><?php echo $product['productCode']; ?></td>
                  <td><?php echo $product['productName']; ?></td>
                  <td><?php 
                  $files = scandir($image_dir_path);
                  $files = array_diff($files, array('.', '..'));
                  $found = false;
                  foreach($files as $filename) {
                      $file_url = $image_dir . DIRECTORY_SEPARATOR . $filename;
                      if(is_file($file_url) && exif_imagetype($file_url)){
                        $path_parts = pathinfo($file_url);
                        if($path_parts['filename'] == $product['productCode']) {
                          $found = true;
                        ?>
                          <img src="<?=$file_url; ?>" alt="imgz" style="width:100%;height:350px;">
                        <?php
                        } 
                      }
                    }
                    if($found != true) {
                      ?>
                      <img src="images\noImage.png" alt="noimg" style="width:100%">
                      <?php
                    }
                  ?></td>
                  <td class="right"><?php echo $product['listPrice']; ?></td>
                  <td>
                    <!-- We are only showing the Delete button for this form -->
                    <form action="delete_product.php" method="post">

                      <!-- This hidden field is used to store the productID -->
                      <input type="hidden" name="product_id_hidden"
                             value="<?php echo $product['productID']; ?>">

                      <!-- This hidden field is used to store the categoryID -->
                      <input type="hidden" name="category_id_hidden"
                             value="<?php echo $product['categoryID']; ?>">

                      <!-- This is the button that we actually see -->
                      <input class="btn btn-warning" type="submit" value="Delete">
                    </form>
                  </td>
                  <td>
                    <!-- We are only showing the Delete button for this form -->
                    <form action="update_product_form.php" method="post">

                      <!-- This hidden field is used to store the productID -->
                      <input type="hidden" name="product_id_hidden"
                             value="<?php echo $product['productID']; ?>">

                      <!-- This hidden field is used to store the categoryID -->
                      <input type="hidden" name="category_id_hidden"
                             value="<?php echo $product['categoryID']; ?>">

                      <!-- This is the button that we actually see -->
                      <input class="btn btn-info" type="submit" value="Update">
                    </form>
                  </td>
              </tr>
              <?php endforeach; ?>
          </tbody>
        </table>
        <a class="btn btn-primary" href="add_product_form.php" role="button">Add Product</a>
        <a class="btn btn-danger" href="logout.php" role="button">Logout</a>
        <a class="btn btn-success" href="add_user_form.php" role="button">Add User</a>
    </section>
</main>
<footer></footer>
</body>
</html>