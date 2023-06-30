<?php

/** @var $pdo \PDO  */
require_once "database.php";
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}
$statement = $pdo->prepare('SELECT * FROM products WHERE id=:id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);
$errors = array();
// initialize values for title and price are empty
$title = $product['title'];
$price = $product['price'];
$description = $product['description'];
function randomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }

    return $str;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['product-title'];
    $description = $_POST['product-description'];
    $price = $_POST['product-price'];
    // need to init the errors array before submitting them (wrong way to do)
    // $errors = array();
    if (!$title) {
        $errors[] = 'Product title is required';
    }
    if (!$price) {
        $errors[] = 'Product price is required';
    }
    // Create a images folder if not already created    
    if (!is_dir('images')) {
        mkdir('images');
    }
    if (empty($errors)) {
        $image = $_FILES['product-image'] ?? null;
        if ($image && $image['tmp_name']) {
            if ($product['image']) {
                unlink($product['image']);
            }
            // create a random folder to store the product image each time the image is submitted
            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            mkdir(dirname($imagePath));
            // move the image in the temporary image path to the product image folder created above
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        $statement = $pdo->prepare("UPDATE products SET title = :title, image = :image, description= :description, price = :price WHERE id = :id");
        // If errors are present, not add them to the database
        // create a named parameter
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        $statement->execute();
        // redirect user back to homepage
        header('Location: index.php');
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-PJsj/BTMqILvmcej7ulplguok8ag4xFTPryRq8xevL7eBYSmpXKcbNVuy+P0RMgq" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Update Product <?php echo $product['title']; ?></title>
</head>

<body>
    <div class="container">
        <p>
            <a href="index.php" class="btn btn-secondary">Back to Homepage</a>
        </p>
        <h1>Update Product <?php echo $product['title']; ?></h1>
        <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error) : ?>
                    <div><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?> <!-- Error validation section -->
        <form action="" method="POST" enctype="multipart/form-data">
            <?php if ($product['image']) : ?>
                <img src="<?php echo $product['image']; ?>" class="update-image">
            <?php endif; ?>
            <div class="mb-3">
                <label for="product-image" class="form-label">Product Image</label>
                <br>
                <input type="file" id="product-image" name="product-image">
            </div>
            <div class="mb-3">
                <label for="product-title" class="form-label">Product Title</label>
                <input type="text" class="form-control" id="product-title" name="product-title" value="<?php echo $title; ?>">
            </div>
            <div class="mb-3">
                <label for="product-description" class="form-label">Product Description</label>
                <textarea id="product-description" class="form-control" name="product-description" ?><?php echo $description; ?></textarea>
            </div>
            <div class=" mb-3">
                <label for="product-price" class="form-label">Product Price</label>
                <input type="number" step=".01" class="form-control" id="product-price" name="product-price" value="<?php echo $price; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

</body>

</html>
