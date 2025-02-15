<?php
// Not an optimal ways
/** @var $pdo \PDO  */
require_once "database.php";
$search = $_GET['search'] ?? '';
print_r($search);
if ($search) {
    $statement = $pdo->prepare('SELECT * FROM products WHERE title LIKE :title ORDER BY create_date DESC');
    $statement->bindValue(':title', "%$search%");
} else {
    $statement = $pdo->prepare('SELECT * FROM products ORDER BY create_date DESC');
}
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include_once "views/partials/header.php" ?>
<h1>Product CRUD</h1>
<p>
    <a href="create.php" class="btn btn-success">Create Product</a>
</p>
<form action="" method="GET">
    <div class="input-group mb-3">
        <input type="text" name="search" id="search" class="form-control" placeholder="Search for products" value="<?php echo $search; ?>">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Image</th>
            <th scope="col">Title</th>
            <th scope="col">Price</th>
            <th scope="col">Create Date</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody class="table-group-divider">
        <?php foreach ($products as $i => $product) : ?>
            <tr>
                <th scope="row"><?php echo $i + 1 ?></th>
                <td>
                    <?php if ($product['image']) : ?>
                        <img class="thumb-image" src="<?php echo $product['image'] ?>" alt="<?php echo $product['title'] ?>">
                    <?php endif; ?>
                </td>
                <td><?php echo $product['title'] ?></td>
                <td><?php echo $product['price'] ?></td>
                <td><?php echo $product['create_date'] ?></td>
                <td>
                    <a href="update.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <form style="display: inline-block;" action="delete.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $product['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

</body>

</html>
